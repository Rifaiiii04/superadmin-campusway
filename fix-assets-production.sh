#!/bin/bash

echo "🔧 FIXING PRODUCTION ASSETS"
echo "==========================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
sudo systemctl stop apache2

# 3. Install dependencies
echo "🔧 Step 1: Installing dependencies..."
npm install

# 4. Build assets
echo "🔧 Step 2: Building assets..."
npm run build

# 5. Check what assets were created
echo "🔧 Step 3: Checking created assets..."
ls -la public/build/assets/ | head -10

# 6. Get actual asset names from manifest
echo "🔧 Step 4: Getting actual asset names..."
JS_ASSET=$(grep -oP '"resources/js/app.jsx":\s*\{\s*"file":\s*"\K[^"]+' public/build/manifest.json)
CSS_ASSET=$(grep -oP '"resources/js/app.jsx":\s*\{\s*[^}]*"css":\s*\[\s*"\K[^"]+' public/build/manifest.json)

echo "JS Asset: $JS_ASSET"
echo "CSS Asset: $CSS_ASSET"

# 7. Update app.blade.php with correct asset names
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
        
        <!-- Production assets -->
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

# 8. Clear caches
echo "🔧 Step 6: Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 9. Set permissions
echo "🔧 Step 7: Setting permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache public
sudo chmod -R 775 storage bootstrap/cache
sudo chmod -R 755 public

# 10. Start Apache
echo "🔄 Step 8: Starting Apache..."
sudo systemctl start apache2

# 11. Test everything
echo "🧪 Step 9: Testing everything..."
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

echo "✅ PRODUCTION ASSETS FIX COMPLETE!"
echo "=================================="
echo "🌐 SuperAdmin Login: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Built assets with Vite"
echo "   ✅ Updated app.blade.php with correct asset names"
echo "   ✅ Fixed all permissions"
echo "   ✅ Cleared all caches"
echo ""
echo "🎉 SuperAdmin should now show login form!"
