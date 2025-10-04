#!/bin/bash

echo "🔧 FIX LARAVEL PUBLIC PATH CONFIGURATION"
echo "========================================"

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Check current .env configuration
echo "🔍 Step 1: Checking current .env configuration..."
if [ -f ".env" ]; then
    echo "=== CURRENT .ENV ==="
    grep -E "APP_URL|APP_ENV|APP_DEBUG" .env
else
    echo "❌ .env file not found"
fi
echo ""

# 3. Update .env for production
echo "🔧 Step 2: Updating .env for production..."
sudo tee -a .env > /dev/null << 'EOF'

# SuperAdmin specific configuration
APP_URL=http://103.23.198.101/super-admin
ASSET_URL=http://103.23.198.101/super-admin
EOF

# 4. Check app.blade.php configuration
echo "🔧 Step 3: Checking app.blade.php configuration..."
if [ -f "resources/views/app.blade.php" ]; then
    echo "=== CURRENT APP.BLADE.PHP ==="
    grep -A 5 -B 5 "build" resources/views/app.blade.php
else
    echo "❌ app.blade.php not found"
fi
echo ""

# 5. Get actual asset names from manifest
echo "🔧 Step 4: Getting actual asset names..."
if [ -f "public/build/manifest.json" ]; then
    JS_ASSET=$(grep -oP '"resources/js/app.jsx":\s*\{\s*"file":\s*"\K[^"]+' public/build/manifest.json)
    CSS_ASSET=$(grep -oP '"resources/js/app.jsx":\s*\{\s*[^}]*"css":\s*\[\s*"\K[^"]+' public/build/manifest.json)
    echo "JS Asset: $JS_ASSET"
    echo "CSS Asset: $CSS_ASSET"
    
    # 6. Update app.blade.php with correct asset names
    echo "🔧 Step 5: Updating app.blade.php with correct asset names..."
    sudo tee resources/views/app.blade.php > /dev/null << EOF
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
        
        <!-- Production assets with correct names -->
        <script type="module" src="./build/$JS_ASSET"></script>
        <link rel="stylesheet" href="./build/$CSS_ASSET">
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
EOF
else
    echo "❌ manifest.json not found - need to build assets first"
fi
echo ""

# 7. Clear Laravel caches
echo "🔧 Step 6: Clearing Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache

# 8. Test Laravel configuration
echo "🔧 Step 7: Testing Laravel configuration..."
php artisan config:show app.url
php artisan config:show app.asset_url

echo "✅ Laravel Public Path Configuration Fixed!"
echo "=========================================="
