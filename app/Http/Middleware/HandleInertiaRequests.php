<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware  // PASTIKAN EXTENDS Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     */
    public function share(Request $request): array
    {
        $admin = $request->user('admin');
        
        // Security: Only share non-sensitive admin data
        $adminData = null;
        if ($admin) {
            $adminData = [
                'id' => $admin->id,
                'username' => $admin->username,
                'name' => $admin->name ?? null,
                // DO NOT share password, tokens, or other sensitive data
            ];
        }
        
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
                'admin' => $adminData, // Only non-sensitive data
            ],
            'csrf_token' => csrf_token(),
        ]);
    }
}
