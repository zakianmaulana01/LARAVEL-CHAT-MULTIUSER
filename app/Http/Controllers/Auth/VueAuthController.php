<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class VueAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->user()->update(['last_seen' => now()]);

            return response()->json([
                'user' => $request->user()->only(['id', 'name', 'email', 'role', 'avatar']),
                'message' => 'Login berhasil!',
            ]);
        }

        return response()->json([
            'message' => 'Email atau password salah.',
        ], 422);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'last_seen' => now(),
        ]);

        Auth::login($user);

        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'role', 'avatar']),
            'message' => 'Registrasi berhasil!',
        ], 201);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout berhasil.']);
    }

    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()->only(['id', 'name', 'email', 'role', 'avatar']),
        ]);
    }
}
