<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Socialite\Facades\Socialite;


class AuthController extends Controller
{
    public function store(RegisterRequest $request): Response
    {
        $user = User::create([
            "name" => $request->input("name"),
            "email" => $request->input("email"),
            "password" => Hash::make($request->input("password")),
        ]);

        Auth::login($user);

        return back();
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $remember = $request->boolean("remember", false);

        if (Auth::attempt([
            "email" => $request->email,
            "password" => $request->password,
        ], $remember)) {
            return back();
        }

        return back()->withErrors([
            "loginError" => __("Invalid password or username."),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("home");
    }

    public function github()
    {
        return Socialite::driver('github')->redirect();
    }
    public function githubRedirect()
    {
        $user = Socialite::driver('github')->user();
        dd($user);
    }

}
