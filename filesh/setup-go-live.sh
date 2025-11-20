#!/bin/bash

echo "🚀 SETUP GO LIVE - Making Web Accessible to Public"
echo "==================================================="
echo ""

# 1. Ensure Apache is running
echo "1️⃣ Ensuring Apache is running..."
sudo systemctl enable apache2
sudo systemctl start apache2
if systemctl is-active --quiet apache2; then
    echo "✅ Apache is running"
else
    echo "❌ Failed to start Apache"
    exit 1
fi
echo ""

# 2. Enable required Apache modules
echo "2️⃣ Enabling required Apache modules..."
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod proxy
sudo a2enmod proxy_http
echo ""

# 3. Ensure Apache config is correct
echo "3️⃣ Verifying Apache configuration..."
if [ -f "/etc/apache2/sites-available/103.23.198.101.conf" ]; then
    echo "✅ Apache config file exists"
    sudo apache2ctl configtest
    if [ $? -ne 0 ]; then
        echo "❌ Apache configuration has errors!"
        exit 1
    fi
else
    echo "⚠️  Apache config file not found at expected location"
    echo "   Current config files:"
    ls -la /etc/apache2/sites-available/
fi
echo ""

# 4. Reload Apache
echo "4️⃣ Reloading Apache..."
sudo systemctl reload apache2
if [ $? -eq 0 ]; then
    echo "✅ Apache reloaded successfully"
else
    echo "❌ Failed to reload Apache"
    exit 1
fi
echo ""

# 5. Check if port 80 is listening
echo "5️⃣ Verifying port 80 is listening..."
sleep 2
if sudo netstat -tlnp | grep -q ":80.*apache2"; then
    echo "✅ Apache is listening on port 80"
    sudo netstat -tlnp | grep ":80"
else
    echo "❌ Apache is not listening on port 80"
    exit 1
fi
echo ""

# 6. Test localhost
echo "6️⃣ Testing localhost access..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/)
if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ Localhost access works (HTTP $HTTP_CODE)"
else
    echo "⚠️  Localhost returned HTTP $HTTP_CODE"
fi
echo ""

# 7. Check firewall status
echo "7️⃣ Checking firewall status..."
if command -v ufw &> /dev/null; then
    UFW_STATUS=$(sudo ufw status | head -1)
    if echo "$UFW_STATUS" | grep -q "inactive"; then
        echo "ℹ️  UFW is inactive (cloud firewall should be configured)"
    else
        echo "ℹ️  UFW is active"
        # Ensure port 80 and 443 are open
        sudo ufw allow 80/tcp
        sudo ufw allow 443/tcp
        echo "✅ Port 80 and 443 allowed in UFW"
    fi
else
    echo "ℹ️  UFW not installed"
fi
echo ""

# 8. Show access information
echo "==================================================="
echo "✅ SETUP COMPLETE"
echo "==================================================="
echo ""
echo "🌐 Your web is now configured for public access:"
echo ""
echo "   Main site:     http://103.23.198.101/"
echo "   SuperAdmin:    http://103.23.198.101/super-admin/"
echo "   Teacher:       http://103.23.198.101/teacher/"
echo ""
echo "⚠️  CRITICAL: Open these ports in IdCloudHost dashboard:"
echo "   1. Login to IdCloudHost dashboard"
echo "   2. Go to Firewall/Security settings"
echo "   3. Add rule: Allow TCP port 80 (HTTP)"
echo "   4. Add rule: Allow TCP port 443 (HTTPS - optional)"
echo "   5. Add rule: Allow TCP port 3000 (if SuperAdmin Backend needed)"
echo ""
echo "🧪 Test from your local computer:"
echo "   curl http://103.23.198.101/"
echo "   or open in browser: http://103.23.198.101/"
echo ""
echo "📋 If web is not accessible from outside:"
echo "   1. Check IdCloudHost firewall settings"
echo "   2. Check Apache logs: sudo tail -f /var/log/apache2/103.23.198.101_error.log"
echo "   3. Run checklist: bash go-live-checklist.sh"
echo ""

