<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated with admin guard
        if (!Auth::guard('admin')->check()) {
            // If AJAX request, return JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated',
                    'redirect' => '/super-admin/login'
                ], 401);
            }
            
            // Redirect to super admin login
            return redirect('/super-admin/login');
        }

        // Ensure we're using the admin guard for all auth operations
        $request->setUserResolver(function () {
            return Auth::guard('admin')->user();
        });

        return $next($request);
    }
}
