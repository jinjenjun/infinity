<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\Gender;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', 'min:8', 'max:12'],
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'gender'   => 'nullable|integer|in:0,1,2',
            'age'      => 'nullable|integer|min:0|max:150',
        ], [
            'password.min'       => '密碼最少 8 個字元',
            'password.max'       => '密碼最多 12 個字元',
            'password.confirmed' => '兩次密碼輸入不一致',
            'phone.max'          => '電話號碼最多 20 個字元',
            'gender.in'          => '性別選項無效',
            'age.integer'        => '年齡必須是數字',
            'age.min'            => '年齡不能小於 0',
            'age.max'            => '年齡不能大於 150',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'address'  => $request->address,
            'birthday' => $request->birthday,
            'gender'   => $request->gender,
            'age'      => $request->age,
        ]);

        $user->assignRole('user');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}