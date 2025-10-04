#!/bin/bash

echo "⚡ QUICK FIX FOR 404"
echo "==================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
sudo systemctl stop apache2

# 3. Create simple Apache config
echo "🔧 Step 1: Creating simple Apache config..."
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

# 4. Enable modules
echo "🔧 Step 2: Enabling modules..."
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod alias

# 5. Test Apache config
echo "🔧 Step 3: Testing Apache config..."
sudo apache2ctl configtest

# 6. Start Apache
echo "🔄 Step 4: Starting Apache..."
sudo systemctl start apache2

# 7. Clear caches
echo "🔧 Step 5: Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 8. Set permissions
echo "🔧 Step 6: Setting permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache public
sudo chmod -R 775 storage bootstrap/cache
sudo chmod -R 755 public

# 9. Test everything
echo "🧪 Step 7: Testing everything..."
echo "Testing SuperAdmin root:"
curl -I http://103.23.198.101/super-admin/ || echo "❌ Root failed"
echo ""

echo "Testing SuperAdmin login:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ Login failed"
echo ""

echo "Testing SuperAdmin test:"
curl -s http://103.23.198.101/super-admin/test || echo "❌ Test failed"
echo ""

echo "Testing Next.js:"
curl -I http://103.23.198.101/arahpotensi/ || echo "❌ Next.js failed"
echo ""

echo "✅ QUICK FIX COMPLETE!"
echo "====================="
echo "🌐 SuperAdmin: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/arahpotensi/"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Simplified Apache config"
echo "   ✅ Fixed DocumentRoot"
echo "   ✅ Enabled required modules"
echo "   ✅ Fixed all permissions"
echo "   ✅ Cleared all caches"
echo ""
echo "🎉 SuperAdmin should now work!"
