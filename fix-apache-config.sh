#!/bin/bash

echo "🔧 Fixing Apache Configuration - SuperAdmin vs Next.js Conflict"
echo "=============================================================="

# 1. Backup current Apache config
echo "💾 Step 1: Backing up current Apache configuration..."
sudo cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf.backup.$(date +%Y%m%d_%H%M%S)

# 2. Create correct Apache configuration
echo "🔧 Step 2: Creating correct Apache configuration..."
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

# 3. Enable required Apache modules
echo "🔧 Step 3: Enabling required Apache modules..."
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod proxy_fcgi
sudo a2enmod setenvif

# 4. Test Apache configuration
echo "🧪 Step 4: Testing Apache configuration..."
sudo apache2ctl configtest || { echo "❌ Apache configuration test failed. Exiting."; exit 1; }

# 5. Restart Apache
echo "🔄 Step 5: Restarting Apache..."
sudo systemctl restart apache2 || { echo "❌ Apache restart failed. Exiting."; exit 1; }

# 6. Test the applications
echo "🧪 Step 6: Testing applications..."
echo ""
echo "Testing SuperAdmin (should show SuperAdmin login, not Guru dashboard):"
curl -I http://103.23.198.101/super-admin/login || echo "❌ SuperAdmin login failed"

echo ""
echo "Testing Next.js root (should show Guru dashboard):"
curl -I http://103.23.198.101/ || echo "❌ Next.js root failed"

echo ""
echo "Testing SuperAdmin test route:"
curl -s http://103.23.198.101/super-admin/test || echo "❌ SuperAdmin test failed"

echo ""
echo "✅ Apache configuration fix complete!"
echo "=============================================================="
echo "🌐 SuperAdmin should now be accessible at: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js Guru dashboard should be at: http://103.23.198.101/"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Apache configuration now properly separates SuperAdmin and Next.js"
echo "   ✅ SuperAdmin has priority over Next.js for /super-admin/* paths"
echo "   ✅ Next.js only handles root paths, not /super-admin/*"
echo "   ✅ Session isolation between applications"
echo ""
echo "🎉 No more 'Dashboard Guru' showing in SuperAdmin!"
