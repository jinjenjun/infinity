<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;

function makeAdmin(string $email): User
{
    $admin = User::factory()->create(['email' => $email]);
    $admin->assignRole('admin');

    return $admin;
}

function makeProduct(User $admin, string $name): Product
{
    $parent = ProductCategory::create(['name' => 'P'.$admin->id, 'slug' => 'p-'.$admin->id, 'admin_id' => $admin->id]);
    $sub = ProductCategory::create(['name' => 'S'.$admin->id, 'slug' => 's-'.$admin->id, 'admin_id' => $admin->id, 'parent_id' => $parent->id]);

    return Product::create([
        'product_category_id' => $sub->id,
        'admin_id' => $admin->id,
        'name' => $name,
        'price' => 100,
        'stock' => 10,
    ]);
}

beforeEach(function () {
    $this->adminA = makeAdmin('a@example.com');
    $this->adminB = makeAdmin('b@example.com');
    $this->adminC = makeAdmin('c@example.com');

    $this->buyer = User::factory()->create();
    $this->buyer->assignRole('user');
    $this->buyer->admins()->attach($this->adminA->id);

    $productA = makeProduct($this->adminA, 'cup');
    $productB = makeProduct($this->adminB, 'bag');

    $this->order = Order::create([
        'user_id' => $this->buyer->id,
        'order_number' => 'ORD-TEST',
        'status' => 'paid',
        'total' => 100 * 1 + 100 * 2,
        'recipient_name' => 'x',
        'recipient_phone' => '0',
        'recipient_address' => 'x',
    ]);
    OrderItem::create(['order_id' => $this->order->id, 'product_id' => $productA->id, 'product_name' => 'cup', 'price' => 100, 'quantity' => 1]);
    OrderItem::create(['order_id' => $this->order->id, 'product_id' => $productB->id, 'product_name' => 'bag', 'price' => 100, 'quantity' => 2]);
});

test('each admin only sees their own product lines and subtotal', function () {
    $a = $this->actingAs($this->adminA)->get(route('orders.show', $this->order));
    $a->assertOk();
    $a->assertInertia(fn ($page) => $page
        ->where('order.items', fn ($items) => count($items) === 1 && $items[0]['product_name'] === 'cup')
        ->where('order.total', 100)
        ->where('order.is_partial', true));

    $b = $this->actingAs($this->adminB)->get(route('orders.show', $this->order));
    $b->assertInertia(fn ($page) => $page
        ->where('order.items', fn ($items) => count($items) === 1 && $items[0]['product_name'] === 'bag')
        ->where('order.total', 200));
});

test('admin with no product in the order gets 403 and does not list it', function () {
    $this->actingAs($this->adminC)->get(route('orders.show', $this->order))->assertForbidden();

    $this->actingAs($this->adminC)->get(route('orders.index'))
        ->assertInertia(fn ($page) => $page->where('orders', fn ($orders) => count($orders) === 0));
});

test('buyer still sees the full order', function () {
    $this->actingAs($this->buyer)->get(route('orders.show', $this->order))
        ->assertInertia(fn ($page) => $page
            ->where('order.items', fn ($items) => count($items) === 2)
            ->where('order.total', 300));
});
