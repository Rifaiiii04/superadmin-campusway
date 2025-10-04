#!/bin/bash

echo "🔧 Fixing SuperAdmin 500 Error - Middleware Issues..."

# 1. Navigate to the superadmin directory
cd /var/www/superadmin/superadmin-campusway || { echo "❌ Failed to navigate to superadmin-campusway. Exiting."; exit 1; }

# 2. Backup current Kernel.php
echo "💾 Backing up current Kernel.php..."
cp app/Http/Kernel.php app/Http/Kernel.php.backup.$(date +%Y%m%d_%H%M%S)

# 3. Fix Kernel.php - disable problematic middleware
echo "🔧 Fixing Kernel.php..."
cat > app/Http/Kernel.php << 'EOF'
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        // \App\Http\Middleware\PerformanceOptimization::class, // DISABLED - CAUSES 500 ERROR
        // \App\Http\Middleware\RequestTimeout::class, // DISABLED - CAUSES 500 ERROR
    ];

    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
            // \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class, // DISABLED - CAUSES VITE MANIFEST ERROR
        ],

        'api' => [
            \App\Http\Middleware\Cors::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'admin.auth' => \App\Http\Middleware\AdminAuth::class,
        'admin.auth.nocsrf' => \App\Http\Middleware\AdminAuthWithoutCsrf::class,
        'school.auth' => \App\Http\Middleware\SchoolAuth::class,
        'superadmin.auth' => \App\Http\Middleware\SuperAdminAuth::class,
        'cors' => \App\Http\Middleware\Cors::class,
    ];
}
EOF

# 4. Fix app.blade.php - disable Vite for production
echo "🔧 Fixing app.blade.php..."
cat > resources/views/app.blade.php << 'EOF'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @routes
        @inertiaHead
        
        <!-- Manual asset loading tanpa Vite untuk production -->
        @if(app()->environment('production'))
            <script type="module" src="/super-admin/build/assets/app-D_7II1BX.js"></script>
            <link rel="stylesheet" href="/super-admin/build/assets/app-BHSs9Ase.css">
        @else
            @viteReactRefresh
            @vite(['resources/js/app.jsx', "resources/js/Pages/{$page['component']}.jsx"])
        @endif
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
EOF

# 5. Clear all caches
echo "🧹 Clearing all caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize:clear

# 6. Test the application
echo "🧪 Testing the application..."
echo "Testing route /test..."
curl -s http://103.23.198.101/super-admin/test || echo "❌ Test route failed"

echo "Testing login page..."
curl -I http://103.23.198.101/super-admin/login || echo "❌ Login page failed"

echo "✅ Middleware fix complete!"
echo "🌐 Test the application at: http://103.23.198.101/super-admin/login"
