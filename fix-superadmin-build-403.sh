#!/bin/bash

echo "🔧 FIX SUPERADMIN BUILD 403"
echo "==========================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
sudo systemctl stop apache2

# 3. Check current Apache config
echo "🔍 Step 1: Checking current Apache config..."
sudo cat /etc/apache2/sites-available/103.23.198.101.conf
echo ""

# 4. Create Apache config with build access
echo "🔧 Step 2: Creating Apache config with build access..."
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
    # SUPERADMIN BUILD ASSETS - CRITICAL
    # =======================
    Alias /super-admin/build /var/www/superadmin/superadmin-campusway/public/build
    <Directory /var/www/superadmin/superadmin-campusway/public/build>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        
        # Allow access to all files in build directory
        <FilesMatch "\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$">
            Require all granted
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

# 5. Enable modules
echo "🔧 Step 3: Enabling modules..."
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod alias

# 6. Test Apache config
echo "🔧 Step 4: Testing Apache config..."
sudo apache2ctl configtest

# 7. Start Apache
echo "🔄 Step 5: Starting Apache..."
sudo systemctl start apache2

# 8. Check build directory
echo "🔍 Step 6: Checking build directory..."
ls -la /var/www/superadmin/superadmin-campusway/public/build/
echo ""

# 9. Check assets directory
echo "🔍 Step 7: Checking assets directory..."
ls -la /var/www/superadmin/superadmin-campusway/public/build/assets/
echo ""

# 10. Set permissions
echo "🔧 Step 8: Setting permissions..."
sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway/public/build
sudo chmod -R 755 /var/www/superadmin/superadmin-campusway/public/build

# 11. Clear caches
echo "🔧 Step 9: Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 12. Test everything
echo "🧪 Step 10: Testing everything..."
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

echo "✅ SUPERADMIN BUILD 403 FIX COMPLETE!"
echo "====================================="
echo "🌐 SuperAdmin: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Added Alias for /super-admin/build"
echo "   ✅ Added Directory config for build assets"
echo "   ✅ Enabled required modules"
echo "   ✅ Fixed file access permissions"
echo "   ✅ Cleared all caches"
echo ""
echo "🎉 SuperAdmin should now load assets correctly!"
