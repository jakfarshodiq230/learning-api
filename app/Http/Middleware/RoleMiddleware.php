<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Pastikan pengguna sudah login
        if (!auth()->Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Periksa role pengguna
        if (auth()->Auth::user()->role !== $role) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
