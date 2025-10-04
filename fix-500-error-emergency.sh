#!/bin/bash

echo "🚨 EMERGENCY FIX 500 INTERNAL SERVER ERROR!"
echo "==========================================="

# 1. Check Apache error logs
echo "🔍 Step 1: Checking Apache error logs..."
sudo tail -n 20 /var/log/apache2/error.log
echo ""

# 2. Check Laravel logs
echo "🔍 Step 2: Checking Laravel logs..."
if [ -f "/var/www/superadmin/superadmin-campusway/storage/logs/laravel.log" ]; then
    sudo tail -n 20 /var/www/superadmin/superadmin-campusway/storage/logs/laravel.log
else
    echo "❌ Laravel log not found"
fi
echo ""

# 3. Check PHP error logs
echo "🔍 Step 3: Checking PHP error logs..."
sudo tail -n 20 /var/log/php8.3-fpm.log
echo ""

# 4. Test Apache config
echo "🔧 Step 4: Testing Apache config..."
sudo apache2ctl configtest
echo ""

# 5. Fix Apache config (simplified)
echo "🔧 Step 5: Fixing Apache config (simplified)..."
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

# 6. Fix permissions
echo "🔧 Step 6: Fixing permissions..."
sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway
sudo chmod -R 755 /var/www/superadmin/superadmin-campusway/public
sudo chmod -R 775 /var/www/superadmin/superadmin-campusway/storage
sudo chmod -R 775 /var/www/superadmin/superadmin-campusway/bootstrap/cache

# 7. Clear Laravel caches
echo "🔧 Step 7: Clearing Laravel caches..."
cd /var/www/superadmin/superadmin-campusway
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 8. Test Apache config again
echo "🔧 Step 8: Testing Apache config again..."
sudo apache2ctl configtest

# 9. Restart Apache
echo "🔄 Step 9: Restarting Apache..."
sudo systemctl restart apache2

# 10. Test everything
echo "🧪 Step 10: Testing everything..."
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

echo "✅ 500 ERROR EMERGENCY FIX COMPLETE!"
echo "===================================="
echo "🌐 SuperAdmin: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Simplified Apache config"
echo "   ✅ Fixed permissions"
echo "   ✅ Cleared Laravel caches"
echo "   ✅ Restarted Apache"
echo ""
echo "🎉 500 error should be fixed now!"
