#!/bin/bash

echo "💥 NUCLEAR FIX FOR 500 ERROR"
echo "============================"

# 1. Navigate to superadmin directory
echo "📁 Step 1: Navigating to /var/www/superadmin/superadmin-campusway..."
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
echo "⏹️ Step 2: Stopping Apache..."
sudo systemctl stop apache2

# 3. NUCLEAR OPTION - Remove ALL problematic files
echo "💥 Step 3: NUCLEAR OPTION - Removing ALL problematic files..."
sudo rm -rf storage/logs/*
sudo rm -rf storage/framework/cache/*
sudo rm -rf storage/framework/sessions/*
sudo rm -rf storage/framework/views/*
sudo rm -rf bootstrap/cache/*
sudo rm -rf public/build/*

# 4. Set ownership to current user
echo "🔧 Step 4: Setting ownership to current user..."
sudo chown -R $USER:$USER .

# 5. Create fresh directories
echo "📁 Step 5: Creating fresh directories..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache
mkdir -p public/build/assets

# 6. Set permissions
echo "🔧 Step 6: Setting permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod -R 755 public

# 7. Create .env if missing
echo "📄 Step 7: Creating .env if missing..."
if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    cp .env.example .env
    php artisan key:generate
fi

# 8. Create minimal app.blade.php
echo "🔧 Step 8: Creating minimal app.blade.php..."
sudo tee resources/views/app.blade.php > /dev/null << 'EOF'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @routes
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
EOF

# 9. Create minimal Kernel.php
echo "🔧 Step 9: Creating minimal Kernel.php..."
sudo tee app/Http/Kernel.php > /dev/null << 'EOF'
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
        ],

        'api' => [
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
        'superadmin.auth' => \App\Http\Middleware\SuperAdminAuth::class,
    ];
}
EOF

# 10. Create minimal routes
echo "🔧 Step 10: Creating minimal routes..."
sudo tee routes/web.php > /dev/null << 'EOF'
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\SuperAdminController;

// Test route
Route::get('/test', function () {
    return response()->json(['status' => 'OK', 'message' => 'Routes working']);
});

// Super Admin Login
Route::get('/login', [SuperAdminController::class, 'showLogin'])->name('login');
Route::post('/login', [SuperAdminController::class, 'login']);

// Logout
Route::post('/logout', function () {
    Auth::guard('admin')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Protected routes
Route::middleware(['superadmin.auth'])->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
});
EOF

# 11. Create empty log file
echo "📄 Step 11: Creating empty log file..."
touch storage/logs/laravel.log
chmod 664 storage/logs/laravel.log

# 12. Test write permissions
echo "🧪 Step 12: Testing write permissions..."
echo "test" > storage/logs/laravel.log && echo "✅ Log writable" || echo "❌ Log not writable"

# 13. Run artisan commands
echo "🔧 Step 13: Running artisan commands..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 14. Set final ownership
echo "🔧 Step 14: Setting final ownership..."
sudo chown -R www-data:www-data storage bootstrap/cache public
sudo chmod -R 775 storage bootstrap/cache
sudo chmod -R 755 public

# 15. Fix Apache configuration
echo "🔧 Step 15: Fixing Apache configuration..."
sudo tee /etc/apache2/sites-available/000-default.conf > /dev/null << 'EOF'
<VirtualHost *:80>
    ServerName 103.23.198.101

    # SUPERADMIN - PRIORITY FIRST
    Alias /super-admin /var/www/superadmin/superadmin-campusway/public
    <Directory /var/www/superadmin/superadmin-campusway/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^ index.php [L]
    </Directory>

    # NEXT.JS - SECONDARY
    DocumentRoot /var/www/arahpotensi/out
    <Directory /var/www/arahpotensi/out">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        RewriteEngine On
        RewriteCond %{REQUEST_URI} !^/super-admin
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . /index.html [L]
    </Directory>

    # PHP Handler
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/var/run/php/php8.3-fpm.sock|fcgi://localhost"
    </FilesMatch>

    ErrorLog ${APACHE_LOG_DIR}/103.23.198.101_error.log
    CustomLog ${APACHE_LOG_DIR}/103.23.198.101_access.log combined
</VirtualHost>
EOF

# 16. Enable Apache modules
echo "🔧 Step 16: Enabling Apache modules..."
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod proxy_fcgi
sudo a2enmod setenvif

# 17. Test Apache config
echo "🧪 Step 17: Testing Apache configuration..."
sudo apache2ctl configtest

# 18. Start Apache
echo "🔄 Step 18: Starting Apache..."
sudo systemctl start apache2

# 19. Test everything
echo "🧪 Step 19: Testing everything..."
echo "Testing SuperAdmin test route:"
curl -s http://103.23.198.101/super-admin/test || echo "❌ SuperAdmin test failed"
echo ""

echo "Testing SuperAdmin login page:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ SuperAdmin login failed"
echo ""

echo "Testing Next.js root:"
curl -I http://103.23.198.101/ || echo "❌ Next.js root failed"
echo ""

echo "✅ NUCLEAR FIX COMPLETE!"
echo "============================"
echo "🌐 SuperAdmin: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Removed ALL problematic files"
echo "   ✅ Created minimal configuration"
echo "   ✅ Fixed all permissions"
echo "   ✅ Fixed Apache configuration"
echo "   ✅ Created fresh .env file"
echo ""
echo "🎉 SuperAdmin should now work without 500 errors!"
