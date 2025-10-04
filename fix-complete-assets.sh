#!/bin/bash

echo "🔧 COMPLETE FIX FOR ASSETS"
echo "========================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
sudo systemctl stop apache2

# 3. Remove old build directory
echo "🔧 Step 1: Removing old build directory..."
sudo rm -rf public/build/*

# 4. Install dependencies
echo "🔧 Step 2: Installing dependencies..."
npm install

# 5. Build assets
echo "🔧 Step 3: Building assets..."
npm run build

# 6. Check what was built
echo "🔧 Step 4: Checking built assets..."
ls -la public/build/
ls -la public/build/assets/ | head -10

# 7. Get actual asset names
echo "🔧 Step 5: Getting actual asset names..."
if [ -f "public/build/manifest.json" ]; then
    JS_ASSET=$(grep -oP '"resources/js/app.jsx":\s*\{\s*"file":\s*"\K[^"]+' public/build/manifest.json)
    CSS_ASSET=$(grep -oP '"resources/js/app.jsx":\s*\{\s*[^}]*"css":\s*\[\s*"\K[^"]+' public/build/manifest.json)
    echo "JS Asset: $JS_ASSET"
    echo "CSS Asset: $CSS_ASSET"
else
    echo "❌ manifest.json not found after build"
    exit 1
fi

# 8. Fix Apache config - Use the correct config file
echo "🔧 Step 6: Fixing Apache config..."
sudo tee /etc/apache2/sites-available/103.23.198.101.conf > /dev/null << 'EOF'
<VirtualHost *:80>
    ServerName 103.23.198.101

    # SUPERADMIN - MAIN CONFIG
    DocumentRoot /var/www/superadmin/superadmin-campusway/public
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
    Alias /arahpotensi /var/www/arahpotensi/out
    <Directory /var/www/arahpotensi/out>
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

# 9. Update app.blade.php with correct asset names
echo "🔧 Step 7: Updating app.blade.php..."
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

# 10. Enable modules
echo "🔧 Step 8: Enabling modules..."
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod alias

# 11. Test Apache config
echo "🔧 Step 9: Testing Apache config..."
sudo apache2ctl configtest

# 12. Start Apache
echo "🔄 Step 10: Starting Apache..."
sudo systemctl start apache2

# 13. Clear caches
echo "🔧 Step 11: Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 14. Set permissions
echo "🔧 Step 12: Setting permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache public
sudo chmod -R 775 storage bootstrap/cache
sudo chmod -R 755 public

# 15. Test everything
echo "🧪 Step 13: Testing everything..."
echo "Testing SuperAdmin login:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ Login failed"
echo ""

echo "Testing build assets:"
curl -I http://103.23.198.101/super-admin/build/$JS_ASSET || echo "❌ JS asset failed"
curl -I http://103.23.198.101/super-admin/build/$CSS_ASSET || echo "❌ CSS asset failed"
echo ""

echo "Testing SuperAdmin test:"
curl -s http://103.23.198.101/super-admin/test || echo "❌ Test failed"
echo ""

echo "✅ COMPLETE FIX COMPLETE!"
echo "========================"
echo "🌐 SuperAdmin Login: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/arahpotensi/"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Rebuilt assets with Vite"
echo "   ✅ Fixed Apache config (103.23.198.101.conf)"
echo "   ✅ Updated app.blade.php with correct asset names"
echo "   ✅ Fixed all permissions"
echo "   ✅ Cleared all caches"
echo ""
echo "🎉 SuperAdmin should now work with assets!"
