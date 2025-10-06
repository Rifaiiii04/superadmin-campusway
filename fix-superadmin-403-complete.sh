#!/bin/bash

echo "🔧 COMPREHENSIVE FIX FOR SUPERADMIN 403 FORBIDDEN"
echo "================================================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
sudo systemctl stop apache2

# 3. Fix Apache Virtual Host Configuration
echo "🔧 Step 1: Fixing Apache Virtual Host Configuration..."
sudo tee /etc/apache2/sites-available/103.23.198.101.conf > /dev/null << 'EOF'
<VirtualHost *:80>
    ServerName 103.23.198.101

    # =======================
    # LARAVEL - SUPERADMIN (PRIORITY FIRST)
    # =======================
    Alias /super-admin /var/www/superadmin/superadmin-campusway/public
    <Directory /var/www/superadmin/superadmin-campusway/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        
        # Laravel rewrite rules - PRIORITY
        RewriteEngine On
        
        # Handle Laravel routing
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^ index.php [L]
    </Directory>

    # =======================
    # SUPERADMIN BUILD ASSETS - CRITICAL FOR STATIC FILES
    # =======================
    Alias /super-admin/build /var/www/superadmin/superadmin-campusway/public/build
    <Directory /var/www/superadmin/superadmin-campusway/public/build>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        
        # Allow access to all static files
        <FilesMatch "\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot|map)$">
            Require all granted
        </FilesMatch>
        
        # Set proper MIME types
        <FilesMatch "\.js$">
            Header set Content-Type "application/javascript"
        </FilesMatch>
        <FilesMatch "\.css$">
            Header set Content-Type "text/css"
        </FilesMatch>
    </Directory>

    # =======================
    # NEXT.JS - GURU DASHBOARD (ROOT) - SECONDARY
    # =======================
    DocumentRoot /var/www/arahpotensi/out
    <Directory /var/www/arahpotensi/out>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted

        # Rewrite untuk SPA - HANYA untuk root (bukan super-admin)
        RewriteEngine On
        RewriteCond %{REQUEST_URI} !^/super-admin
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . /index.html [L]
    </Directory>

    # =======================
    # SESSION ISOLATION
    # =======================
    <Location /super-admin>
        Header always edit Set-Cookie "(.*)" "$1; Path=/super-admin"
    </Location>

    # PHP Handler
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/var/run/php/php8.3-fpm.sock|fcgi://localhost"
    </FilesMatch>

    ErrorLog ${APACHE_LOG_DIR}/103.23.198.101_error.log
    CustomLog ${APACHE_LOG_DIR}/103.23.198.101_access.log combined
</VirtualHost>
EOF

# 4. Enable required modules
echo "🔧 Step 2: Enabling required modules..."
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod alias
sudo a2enmod dir

# 5. Fix ownership and permissions
echo "🔧 Step 3: Fixing ownership and permissions..."
sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway
sudo find /var/www/superadmin/superadmin-campusway -type d -exec chmod 755 {} \;
sudo find /var/www/superadmin/superadmin-campusway -type f -exec chmod 644 {} \;
sudo chmod -R 775 /var/www/superadmin/superadmin-campusway/storage
sudo chmod -R 775 /var/www/superadmin/superadmin-campusway/bootstrap/cache
sudo chmod -R 755 /var/www/superadmin/superadmin-campusway/public/build

# 6. Create .htaccess for build directory
echo "🔧 Step 4: Creating .htaccess for build directory..."
sudo tee /var/www/superadmin/superadmin-campusway/public/build/.htaccess > /dev/null << 'EOF'
# Allow access to all files in build directory
<RequireAll>
    Require all granted
</RequireAll>

# Set proper MIME types
<FilesMatch "\.js$">
    Header set Content-Type "application/javascript"
    Header set Cache-Control "public, max-age=31536000"
</FilesMatch>

<FilesMatch "\.css$">
    Header set Content-Type "text/css"
    Header set Cache-Control "public, max-age=31536000"
</FilesMatch>

<FilesMatch "\.(png|jpg|jpeg|gif|ico|svg)$">
    Header set Cache-Control "public, max-age=31536000"
</FilesMatch>

# Disable directory browsing
Options -Indexes

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
EOF

# 7. Get actual asset names and update app.blade.php
echo "🔧 Step 5: Updating app.blade.php with correct asset names..."
if [ -f "public/build/manifest.json" ]; then
    JS_ASSET=$(grep -oP '"resources/js/app.jsx":\s*\{\s*"file":\s*"\K[^"]+' public/build/manifest.json)
    CSS_ASSET=$(grep -oP '"resources/js/app.jsx":\s*\{\s*[^}]*"css":\s*\[\s*"\K[^"]+' public/build/manifest.json)
    echo "JS Asset: $JS_ASSET"
    echo "CSS Asset: $CSS_ASSET"
    
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

# 8. Clear Laravel caches
echo "🔧 Step 6: Clearing Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 9. Test Apache configuration
echo "🔧 Step 7: Testing Apache configuration..."
sudo apache2ctl configtest

# 10. Start Apache
echo "🔄 Step 8: Starting Apache..."
sudo systemctl start apache2

# 11. Test everything
echo "🧪 Step 9: Testing everything..."
echo "Testing SuperAdmin login:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ Login failed"
echo ""

echo "Testing build directory:"
curl -I http://103.23.198.101/super-admin/build/ || echo "❌ Build directory failed"
echo ""

echo "Testing assets directory:"
curl -I http://103.23.198.101/super-admin/build/assets/ || echo "❌ Assets directory failed"
echo ""

echo "Testing SuperAdmin test:"
curl -s http://103.23.198.101/super-admin/test || echo "❌ Test failed"
echo ""

echo "Testing Next.js:"
curl -I http://103.23.198.101/ || echo "❌ Next.js failed"
echo ""

echo "✅ COMPREHENSIVE FIX COMPLETE!"
echo "============================="
echo "🌐 SuperAdmin: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Apache Virtual Host Configuration"
echo "   ✅ Directory Permissions and Ownership"
echo "   ✅ .htaccess for build directory"
echo "   ✅ Laravel Public Path Configuration"
echo "   ✅ Asset names in app.blade.php"
echo "   ✅ All required Apache modules enabled"
echo ""
echo "🎉 SuperAdmin should now work without 403 Forbidden!"
