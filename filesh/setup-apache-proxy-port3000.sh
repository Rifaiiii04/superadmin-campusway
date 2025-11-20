#!/bin/bash

echo "🔧 Setting up Apache Reverse Proxy for Port 3000"
echo "================================================="
echo ""

# 1. Enable required Apache modules
echo "1️⃣ Enabling required Apache modules..."
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod rewrite
sudo a2enmod headers
echo ""

# 2. Backup current Apache config
echo "2️⃣ Backing up current Apache configuration..."
sudo cp /etc/apache2/sites-available/103.23.198.101.conf /etc/apache2/sites-available/103.23.198.101.conf.backup.$(date +%Y%m%d_%H%M%S)
echo ""

# 3. Read current config and add proxy configuration
echo "3️⃣ Adding reverse proxy configuration..."
CURRENT_CONFIG="/etc/apache2/sites-available/103.23.198.101.conf"

# Check if proxy configuration already exists
if grep -q "ProxyPass.*3000" "$CURRENT_CONFIG"; then
    echo "⚠️  Proxy configuration for port 3000 already exists"
    read -p "   Do you want to update it? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "   Skipping proxy configuration update"
        exit 0
    fi
fi

# Create temporary config file with proxy settings
sudo tee /tmp/apache-proxy-config.conf > /dev/null << 'PROXY_CONFIG'
# Reverse Proxy for SuperAdmin Backend (Port 3000)
# This allows accessing http://103.23.198.101/super-admin-backend-api/ 
# which will proxy to http://localhost:3000/super-admin/api/

<Location /super-admin-backend-api>
    ProxyPass http://localhost:3000/super-admin/api
    ProxyPassReverse http://localhost:3000/super-admin/api
    ProxyPreserveHost On
    
    # CORS Headers
    Header always set Access-Control-Allow-Origin "*"
    Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header always set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"
    Header always set Access-Control-Allow-Credentials "true"
    
    # Handle OPTIONS requests
    RewriteEngine On
    RewriteCond %{REQUEST_METHOD} OPTIONS
    RewriteRule ^(.*)$ $1 [R=200,L]
</Location>
PROXY_CONFIG

# Add proxy config to main virtual host (before closing VirtualHost tag)
if ! grep -q "super-admin-backend-api" "$CURRENT_CONFIG"; then
    echo "   Adding proxy configuration to Apache config..."
    sudo sed -i '/<\/VirtualHost>/i\
\
# Reverse Proxy for SuperAdmin Backend (Port 3000)\
<Location /super-admin-backend-api>\
    ProxyPass http://localhost:3000/super-admin/api\
    ProxyPassReverse http://localhost:3000/super-admin/api\
    ProxyPreserveHost On\
    \
    # CORS Headers\
    Header always set Access-Control-Allow-Origin "*"\
    Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"\
    Header always set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"\
    Header always set Access-Control-Allow-Credentials "true"\
    \
    # Handle OPTIONS requests\
    RewriteEngine On\
    RewriteCond %{REQUEST_METHOD} OPTIONS\
    RewriteRule ^(.*)$ $1 [R=200,L]\
</Location>\
' "$CURRENT_CONFIG"
fi

# 4. Test Apache configuration
echo "4️⃣ Testing Apache configuration..."
sudo apache2ctl configtest
if [ $? -ne 0 ]; then
    echo "❌ Apache configuration test failed!"
    echo "   Restoring backup..."
    sudo cp /etc/apache2/sites-available/103.23.198.101.conf.backup.* /etc/apache2/sites-available/103.23.198.101.conf
    exit 1
fi
echo ""

# 5. Reload Apache
echo "5️⃣ Reloading Apache..."
sudo systemctl reload apache2
if [ $? -ne 0 ]; then
    echo "❌ Failed to reload Apache!"
    exit 1
fi
echo ""

echo "================================================="
echo "✅ Apache Reverse Proxy configured successfully!"
echo ""
echo "📋 Configuration Summary:"
echo "   - Direct access: http://103.23.198.101:3000"
echo "   - Via Apache proxy: http://103.23.198.101/super-admin-backend-api/"
echo ""
echo "⚠️  Important:"
echo "   1. Make sure superadmin-backend is running on port 3000"
echo "   2. Test with: curl http://localhost:3000/super-admin/api"
echo "   3. Then test proxy: curl http://localhost/super-admin-backend-api/"
echo ""

