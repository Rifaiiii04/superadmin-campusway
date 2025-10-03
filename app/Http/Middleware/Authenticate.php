<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Jika request expect JSON, return null (unauthorized)
        if ($request->expectsJson()) {
            return null;
        }

        // Untuk semua kasus, redirect ke path /login (bukan named route)
        return '/login';
    }
}
