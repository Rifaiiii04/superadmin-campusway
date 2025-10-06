<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated',
                    'redirect' => '/super-admin/login'
                ], 401);
            }
            return redirect('/super-admin/login');
        }
        return $next($request);
    }
}
