#!/bin/bash

echo "🔧 FIXING APACHE BUILD ACCESS"
echo "============================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
sudo systemctl stop apache2

# 3. Create proper Apache config with build access
echo "🔧 Step 1: Creating proper Apache config..."
sudo tee /etc/apache2/sites-available/000-default.conf > /dev/null << 'EOF'
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
    <Directory /var/www/arahpotensi/out">
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

# 5. Test Apache config
echo "🔧 Step 3: Testing Apache configuration..."
sudo apache2ctl configtest

# 6. Start Apache
echo "🔄 Step 4: Starting Apache..."
sudo systemctl start apache2

# 7. Test build access
echo "🧪 Step 5: Testing build access..."
echo "Testing build directory access:"
curl -I http://103.23.198.101/super-admin/build/ || echo "❌ Build directory failed"
echo ""

echo "Testing specific JS asset:"
curl -I http://103.23.198.101/super-admin/build/assets/app-DWYINBfv.js || echo "❌ JS asset failed"
echo ""

echo "Testing specific CSS asset:"
curl -I http://103.23.198.101/super-admin/build/assets/app-d2W3ZRG7.css || echo "❌ CSS asset failed"
echo ""

echo "Testing SuperAdmin login page:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ Login failed"
echo ""

echo "Testing SuperAdmin test:"
curl -s http://103.23.198.101/super-admin/test || echo "❌ Test failed"
echo ""

echo "✅ APACHE BUILD ACCESS FIX COMPLETE!"
echo "===================================="
echo "🌐 SuperAdmin Login: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Added Alias for /super-admin/build"
echo "   ✅ Added Directory config for build assets"
echo "   ✅ Enabled required Apache modules"
echo "   ✅ Fixed file access permissions"
echo ""
echo "🎉 SuperAdmin should now load assets correctly!"
