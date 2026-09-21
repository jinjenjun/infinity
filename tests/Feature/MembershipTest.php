<?php

use App\Models\InviteCode;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;

function makeAdminUser(string $email): User
{
    $admin = User::factory()->create(['email' => $email]);
    $admin->assignRole('admin');

    return $admin;
}

function makeVisibleProduct(User $admin, string $name, string $visibility): Product
{
    $parent = ProductCategory::firstOrCreate(['slug' => 'p-'.$admin->id], ['name' => 'P', 'admin_id' => $admin->id]);
    $sub = ProductCategory::firstOrCreate(['slug' => 's-'.$admin->id], ['name' => 'S', 'admin_id' => $admin->id, 'parent_id' => $parent->id]);

    return Product::create([
        'product_category_id' => $sub->id, 'admin_id' => $admin->id,
        'name' => $name, 'price' => 10, 'stock' => 1, 'visibility' => $visibility,
    ]);
}

test('a user in several admins sees member products of each', function () {
    $a = makeAdminUser('a@example.com');
    $b = makeAdminUser('b@example.com');
    $c = makeAdminUser('c@example.com');
    makeVisibleProduct($a, 'a-member', 'member');
    makeVisibleProduct($b, 'b-member', 'member');
    makeVisibleProduct($c, 'c-member', 'member');
    makeVisibleProduct($a, 'a-private', 'private');

    $user = User::factory()->create();
    $user->admins()->attach([$a->id, $b->id]);

    expect(Product::visibleTo($user)->pluck('name')->sort()->values()->all())->toBe(['a-member', 'b-member']);
});

test('registration with an invite code joins that admin, without one joins nobody', function () {
    $a = makeAdminUser('a@example.com');
    $code = InviteCode::create(['admin_id' => $a->id, 'code' => InviteCode::generate()]);
    $payload = ['password' => 'password', 'password_confirmation' => 'password'];

    $this->post('/register', $payload + ['name' => 'n1', 'email' => 'n1@example.com', 'invite_code' => $code->code]);
    expect(User::where('email', 'n1@example.com')->first()->admins->pluck('id')->all())->toBe([$a->id]);

    auth()->logout();
    $this->post('/register', $payload + ['name' => 'n2', 'email' => 'n2@example.com']);
    expect(User::where('email', 'n2@example.com')->first()->admins)->toHaveCount(0);
});

test('a logged in user can join another admin with an invite code, once', function () {
    $a = makeAdminUser('a@example.com');
    $b = makeAdminUser('b@example.com');
    $user = User::factory()->create();
    $user->assignRole('user');
    $user->admins()->attach($a->id);
    $codeB = InviteCode::create(['admin_id' => $b->id, 'code' => InviteCode::generate()]);

    $this->actingAs($user)->post(route('profile.invite-code'), ['invite_code' => $codeB->code])->assertSessionHasNoErrors();
    expect($user->admins()->count())->toBe(2);

    $this->actingAs($user)->post(route('profile.invite-code'), ['invite_code' => $codeB->code])->assertSessionHasErrors('invite_code');
    expect($user->admins()->count())->toBe(2);
});

test('expired invite code and own code are rejected', function () {
    $a = makeAdminUser('a@example.com');
    $expired = InviteCode::create(['admin_id' => $a->id, 'code' => 'AAAA-BBBB', 'expires_at' => now()->subDay()]);
    $own = InviteCode::create(['admin_id' => $a->id, 'code' => 'CCCC-DDDD']);
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('profile.invite-code'), ['invite_code' => 'AAAA-BBBB'])->assertSessionHasErrors('invite_code');
    $this->actingAs($a)->post(route('profile.invite-code'), ['invite_code' => 'CCCC-DDDD'])->assertSessionHasErrors('invite_code');
    expect($user->admins()->count())->toBe(0);
});
