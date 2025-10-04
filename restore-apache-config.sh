#!/bin/bash

echo "🔧 RESTORE APACHE CONFIG"
echo "======================="

# 1. Stop Apache
sudo systemctl stop apache2

# 2. Restore original Apache config
echo "🔧 Step 1: Restoring original Apache config..."
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

# 3. Enable modules
echo "🔧 Step 2: Enabling modules..."
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod alias

# 4. Test Apache config
echo "🔧 Step 3: Testing Apache config..."
sudo apache2ctl configtest

# 5. Start Apache
echo "🔄 Step 4: Starting Apache..."
sudo systemctl start apache2

# 6. Test everything
echo "🧪 Step 5: Testing everything..."
echo "Testing SuperAdmin:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ SuperAdmin failed"
echo ""

echo "Testing Next.js:"
curl -I http://103.23.198.101/ || echo "❌ Next.js failed"
echo ""

echo "✅ APACHE CONFIG RESTORED!"
echo "========================="
echo "🌐 SuperAdmin: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/"
echo ""
echo "📋 What was restored:"
echo "   ✅ Original Apache config"
echo "   ✅ SuperAdmin alias /super-admin"
echo "   ✅ Next.js DocumentRoot"
echo "   ✅ Session isolation"
echo ""
echo "🎉 Both should work now!"
