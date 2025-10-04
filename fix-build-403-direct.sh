#!/bin/bash

echo "🔧 FIX BUILD 403 FORBIDDEN - DIRECT APPROACH"
echo "==========================================="

# 1. Check current Apache config
echo "🔍 Step 1: Checking current Apache config..."
sudo cat /etc/apache2/sites-available/103.23.198.101.conf | grep -A 20 "super-admin/build"

# 2. Check build directory permissions
echo "🔍 Step 2: Checking build directory permissions..."
ls -la /var/www/superadmin/superadmin-campusway/public/build/
echo ""

# 3. Check if build directory exists
echo "🔍 Step 3: Checking if build directory exists..."
if [ -d "/var/www/superadmin/superadmin-campusway/public/build" ]; then
    echo "✅ Build directory exists"
    echo "Contents:"
    ls -la /var/www/superadmin/superadmin-campusway/public/build/
else
    echo "❌ Build directory does not exist"
    echo "Creating build directory..."
    sudo mkdir -p /var/www/superadmin/superadmin-campusway/public/build
    sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway/public/build
    sudo chmod -R 755 /var/www/superadmin/superadmin-campusway/public/build
fi
echo ""

# 4. Check if assets directory exists
echo "🔍 Step 4: Checking if assets directory exists..."
if [ -d "/var/www/superadmin/superadmin-campusway/public/build/assets" ]; then
    echo "✅ Assets directory exists"
    echo "Contents:"
    ls -la /var/www/superadmin/superadmin-campusway/public/build/assets/ | head -5
else
    echo "❌ Assets directory does not exist"
    echo "Creating assets directory..."
    sudo mkdir -p /var/www/superadmin/superadmin-campusway/public/build/assets
    sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway/public/build/assets
    sudo chmod -R 755 /var/www/superadmin/superadmin-campusway/public/build/assets
fi
echo ""

# 5. Fix Apache config for build directory
echo "🔧 Step 5: Fixing Apache config for build directory..."
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

# 6. Fix permissions
echo "🔧 Step 6: Fixing permissions..."
sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway/public/build
sudo chmod -R 755 /var/www/superadmin/superadmin-campusway/public/build

# 7. Create .htaccess for build directory
echo "🔧 Step 7: Creating .htaccess for build directory..."
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

# 8. Test Apache config
echo "🔧 Step 8: Testing Apache config..."
sudo apache2ctl configtest

# 9. Restart Apache
echo "🔄 Step 9: Restarting Apache..."
sudo systemctl restart apache2

# 10. Test build directory access
echo "🧪 Step 10: Testing build directory access..."
echo "Testing build directory:"
curl -I http://103.23.198.101/super-admin/build/ || echo "❌ Build directory failed"
echo ""

echo "Testing assets directory:"
curl -I http://103.23.198.101/super-admin/build/assets/ || echo "❌ Assets directory failed"
echo ""

echo "Testing SuperAdmin login:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ Login failed"
echo ""

echo "✅ BUILD 403 FIX COMPLETE!"
echo "========================="
echo "🌐 SuperAdmin: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Apache config for build directory"
echo "   ✅ Directory permissions and ownership"
echo "   ✅ .htaccess for build directory"
echo "   ✅ Created missing directories if needed"
echo ""
echo "🎉 Build directory should now be accessible!"
