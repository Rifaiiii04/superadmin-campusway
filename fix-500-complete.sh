#!/bin/bash

echo "🔧 FIX 500 ERROR COMPLETE - COMMAND LENGKAP!"
echo "==========================================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Fix Apache config (complete)
echo "🔧 Step 1: Fixing Apache config (complete)..."
sudo tee /etc/apache2/sites-available/103.23.198.101.conf > /dev/null << 'EOF'
<VirtualHost *:80>
    ServerName 103.23.198.101

    # LARAVEL - SUPERADMIN
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

    # SUPERADMIN BUILD ASSETS
    Alias /super-admin/build /var/www/superadmin/superadmin-campusway/public/build
    <Directory /var/www/superadmin/superadmin-campusway/public/build>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # NEXT.JS - GURU DASHBOARD
    DocumentRoot /var/www/arahpotensi/out
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

# 3. Fix permissions (complete)
echo "🔧 Step 2: Fixing permissions (complete)..."
sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway
sudo chmod -R 755 /var/www/superadmin/superadmin-campusway/public
sudo chmod -R 775 /var/www/superadmin/superadmin-campusway/storage
sudo chmod -R 775 /var/www/superadmin/superadmin-campusway/bootstrap/cache

# 4. Clear Laravel caches
echo "🔧 Step 3: Clearing Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 5. Test Apache config
echo "🔧 Step 4: Testing Apache config..."
sudo apache2ctl configtest

# 6. Restart Apache
echo "🔄 Step 5: Restarting Apache..."
sudo systemctl restart apache2

# 7. Test everything
echo "🧪 Step 6: Testing everything..."
echo "Testing SuperAdmin test:"
curl -s http://103.23.198.101/super-admin/test || echo "❌ Test failed"
echo ""

echo "Testing SuperAdmin login:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ Login failed"
echo ""

echo "Testing build directory:"
curl -I http://103.23.198.101/super-admin/build/ || echo "❌ Build directory failed"
echo ""

echo "Testing Next.js:"
curl -I http://103.23.198.101/ || echo "❌ Next.js failed"
echo ""

echo "✅ 500 ERROR FIX COMPLETE!"
echo "========================="
echo "🌐 SuperAdmin: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Complete Apache config"
echo "   ✅ Fixed permissions"
echo "   ✅ Cleared Laravel caches"
echo "   ✅ Restarted Apache"
echo ""
echo "🎉 500 error should be fixed now!"
