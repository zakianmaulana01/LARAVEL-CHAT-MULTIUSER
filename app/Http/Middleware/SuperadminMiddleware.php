<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperadminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isSuperadmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
            abort(403, 'Akses ditolak. Hanya superadmin yang bisa mengakses halaman ini.');
        }

        return $next($request);
    }
}
