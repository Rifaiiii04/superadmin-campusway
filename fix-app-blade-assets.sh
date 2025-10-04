#!/bin/bash

echo "🔧 FIXING APP.BLADE.PHP ASSETS"
echo "==============================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Get actual asset names from manifest
echo "🔧 Step 1: Getting actual asset names..."
JS_ASSET=$(grep -oP '"resources/js/app.jsx":\s*\{\s*"file":\s*"\K[^"]+' public/build/manifest.json)
CSS_ASSET=$(grep -oP '"resources/js/app.jsx":\s*\{\s*[^}]*"css":\s*\[\s*"\K[^"]+' public/build/manifest.json)

echo "JS Asset: $JS_ASSET"
echo "CSS Asset: $CSS_ASSET"

# 3. Update app.blade.php with correct asset names
echo "🔧 Step 2: Updating app.blade.php with correct asset names..."
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
        @if(app()->environment('production'))
            <script type="module" src="/super-admin/build/$JS_ASSET"></script>
            <link rel="stylesheet" href="/super-admin/build/$CSS_ASSET">
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

# 4. Clear caches
echo "🔧 Step 3: Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 5. Set permissions
echo "🔧 Step 4: Setting permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache public
sudo chmod -R 775 storage bootstrap/cache
sudo chmod -R 755 public

# 6. Test everything
echo "🧪 Step 5: Testing everything..."
echo "Testing SuperAdmin login page:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ Login failed"
echo ""

echo "Testing SuperAdmin test:"
curl -s http://103.23.198.101/super-admin/test || echo "❌ Test failed"
echo ""

echo "Testing asset URLs:"
curl -I http://103.23.198.101/super-admin/build/$JS_ASSET || echo "❌ JS asset failed"
curl -I http://103.23.198.101/super-admin/build/$CSS_ASSET || echo "❌ CSS asset failed"
echo ""

echo "✅ APP.BLADE.PHP ASSETS FIX COMPLETE!"
echo "====================================="
echo "🌐 SuperAdmin Login: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Updated app.blade.php with correct asset names"
echo "   ✅ Fixed all permissions"
echo "   ✅ Cleared all caches"
echo ""
echo "🎉 SuperAdmin should now load assets correctly!"
