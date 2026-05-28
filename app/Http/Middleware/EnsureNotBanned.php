<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->is_banned) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun Anda telah diblokir.'], 403);
            }

            return redirect()->route('blade.login')->with('error', 'Akun Anda telah diblokir oleh administrator.');
        }

        return $next($request);
    }
}
