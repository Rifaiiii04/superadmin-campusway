#!/bin/bash

echo "🔧 FIX DIRECTORY PERMISSIONS"
echo "============================"

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
sudo systemctl stop apache2

# 3. Fix ownership and permissions
echo "🔧 Step 1: Fixing ownership and permissions..."

# Set ownership to www-data for web access
sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway

# Set directory permissions (755 for directories)
sudo find /var/www/superadmin/superadmin-campusway -type d -exec chmod 755 {} \;

# Set file permissions (644 for files)
sudo find /var/www/superadmin/superadmin-campusway -type f -exec chmod 644 {} \;

# Special permissions for storage and bootstrap/cache
sudo chmod -R 775 /var/www/superadmin/superadmin-campusway/storage
sudo chmod -R 775 /var/www/superadmin/superadmin-campusway/bootstrap/cache

# Special permissions for build directory
sudo chmod -R 755 /var/www/superadmin/superadmin-campusway/public/build

# 4. Create .htaccess for build directory
echo "🔧 Step 2: Creating .htaccess for build directory..."
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

# 5. Set permissions for .htaccess
sudo chmod 644 /var/www/superadmin/superadmin-campusway/public/build/.htaccess

# 6. Verify permissions
echo "🔧 Step 3: Verifying permissions..."
echo "=== BUILD DIRECTORY PERMISSIONS ==="
ls -la /var/www/superadmin/superadmin-campusway/public/build/
echo ""
echo "=== ASSETS DIRECTORY PERMISSIONS ==="
ls -la /var/www/superadmin/superadmin-campusway/public/build/assets/ | head -10
echo ""

# 7. Test file access
echo "🔧 Step 4: Testing file access..."
if [ -f "/var/www/superadmin/superadmin-campusway/public/build/manifest.json" ]; then
    echo "✅ manifest.json is accessible"
    echo "=== MANIFEST CONTENT ==="
    head -5 /var/www/superadmin/superadmin-campusway/public/build/manifest.json
else
    echo "❌ manifest.json not found"
fi
echo ""

# 8. Start Apache
echo "🔄 Step 5: Starting Apache..."
sudo systemctl start apache2

echo "✅ Directory Permissions Fixed!"
echo "============================="
