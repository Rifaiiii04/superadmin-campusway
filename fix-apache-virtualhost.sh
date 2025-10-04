#!/bin/bash

echo "🔧 FIX APACHE VIRTUAL HOST CONFIGURATION"
echo "========================================"

# 1. Stop Apache
sudo systemctl stop apache2

# 2. Backup current config
echo "📋 Step 1: Backing up current config..."
sudo cp /etc/apache2/sites-available/103.23.198.101.conf /etc/apache2/sites-available/103.23.198.101.conf.backup

# 3. Create correct Apache virtual host configuration
echo "🔧 Step 2: Creating correct Apache virtual host configuration..."
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
echo "🔧 Step 3: Enabling required modules..."
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod alias
sudo a2enmod dir

# 5. Test Apache configuration
echo "🔧 Step 4: Testing Apache configuration..."
sudo apache2ctl configtest

# 6. Start Apache
echo "🔄 Step 5: Starting Apache..."
sudo systemctl start apache2

echo "✅ Apache Virtual Host Configuration Fixed!"
echo "=========================================="
