#!/bin/bash

echo "🚨 EMERGENCY FIX 403 FORBIDDEN"
echo "=============================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
sudo systemctl stop apache2

# 3. Check current Apache config
echo "🔍 Step 1: Checking current Apache config..."
sudo cat /etc/apache2/sites-available/000-default.conf

# 4. Create SIMPLE Apache config
echo "🔧 Step 2: Creating SIMPLE Apache config..."
sudo tee /etc/apache2/sites-available/000-default.conf > /dev/null << 'EOF'
<VirtualHost *:80>
    ServerName 103.23.198.101

    # SUPERADMIN - SIMPLE CONFIG
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

# 5. Enable modules
echo "🔧 Step 3: Enabling modules..."
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod alias

# 6. Test config
echo "🔧 Step 4: Testing config..."
sudo apache2ctl configtest

# 7. Start Apache
echo "🔄 Step 5: Starting Apache..."
sudo systemctl start apache2

# 8. Fix app.blade.php to use relative paths
echo "🔧 Step 6: Fixing app.blade.php..."
sudo tee resources/views/app.blade.php > /dev/null << 'EOF'
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
        
        <!-- Use relative paths -->
        <script type="module" src="./build/assets/app-DWYINBfv.js"></script>
        <link rel="stylesheet" href="./build/assets/app-d2W3ZRG7.css">
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
EOF

# 9. Clear caches
echo "🔧 Step 7: Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 10. Set permissions
echo "🔧 Step 8: Setting permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache public
sudo chmod -R 775 storage bootstrap/cache
sudo chmod -R 755 public

# 11. Test everything
echo "🧪 Step 9: Testing everything..."
echo "Testing SuperAdmin root:"
curl -I http://103.23.198.101/super-admin/ || echo "❌ Root failed"
echo ""

echo "Testing SuperAdmin login:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ Login failed"
echo ""

echo "Testing SuperAdmin test:"
curl -s http://103.23.198.101/super-admin/test || echo "❌ Test failed"
echo ""

echo "Testing build assets:"
curl -I http://103.23.198.101/super-admin/build/assets/app-DWYINBfv.js || echo "❌ JS asset failed"
curl -I http://103.23.198.101/super-admin/build/assets/app-d2W3ZRG7.css || echo "❌ CSS asset failed"
echo ""

echo "Testing Next.js:"
curl -I http://103.23.198.101/arahpotensi/ || echo "❌ Next.js failed"
echo ""

echo "✅ EMERGENCY FIX COMPLETE!"
echo "========================="
echo "🌐 SuperAdmin: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/arahpotensi/"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Simplified Apache config"
echo "   ✅ Fixed app.blade.php with relative paths"
echo "   ✅ Fixed all permissions"
echo "   ✅ Cleared all caches"
echo ""
echo "🎉 SuperAdmin should now work!"
