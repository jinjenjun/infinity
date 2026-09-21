<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\InviteCode;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'admins' => $request->user()->admins()->pluck('users.name'),
        ]);
    }

    public function joinInviteCode(Request $request): RedirectResponse
    {
        $request->validate(
            ['invite_code' => 'required|string|exists:invite_codes,code'],
            ['invite_code.required' => '請輸入邀請碼', 'invite_code.exists' => '邀請碼無效'],
        );

        $inviteCode = InviteCode::where('code', $request->invite_code)->first();
        $user = $request->user();

        if (! $inviteCode->isValid()) {
            return back()->withErrors(['invite_code' => '邀請碼已過期']);
        }

        if ($inviteCode->admin_id === $user->id) {
            return back()->withErrors(['invite_code' => '不能加入自己']);
        }

        if ($user->admins()->where('users.id', $inviteCode->admin_id)->exists()) {
            return back()->withErrors(['invite_code' => '你已經是這位管理者的會員']);
        }

        $user->admins()->attach($inviteCode->admin_id, ['invite_code_id' => $inviteCode->id]);

        return back()->with('status', 'invite-code-joined');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
