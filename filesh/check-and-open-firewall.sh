#!/bin/bash

echo "🔍 Checking and Opening Firewall for Public Access"
echo "==================================================="
echo ""

# 1. Check UFW status
echo "1️⃣ Checking UFW (Uncomplicated Firewall)..."
if command -v ufw &> /dev/null; then
    UFW_STATUS=$(sudo ufw status | head -1)
    if echo "$UFW_STATUS" | grep -q "inactive"; then
        echo "ℹ️  UFW is inactive"
        echo ""
        echo "   Do you want to enable UFW and open port 80? (y/n)"
        read -p "   " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            echo "   Enabling UFW and opening ports..."
            sudo ufw --force enable
            sudo ufw allow 80/tcp
            sudo ufw allow 443/tcp
            sudo ufw allow 3000/tcp
            sudo ufw allow 22/tcp  # Keep SSH open
            echo "✅ UFW enabled and ports opened"
        else
            echo "   Skipping UFW setup"
        fi
    else
        echo "✅ UFW is active"
        echo "   Current rules:"
        sudo ufw status numbered
        echo ""
        echo "   Checking if port 80 is open..."
        if sudo ufw status | grep -q "80"; then
            echo "✅ Port 80 is already open in UFW"
        else
            echo "⚠️  Port 80 is not open in UFW"
            echo "   Opening port 80..."
            sudo ufw allow 80/tcp
            sudo ufw allow 443/tcp
            sudo ufw allow 3000/tcp
            echo "✅ Ports opened"
        fi
    fi
else
    echo "ℹ️  UFW is not installed"
    echo "   Install with: sudo apt install ufw"
fi
echo ""

# 2. Check iptables
echo "2️⃣ Checking iptables rules..."
if sudo iptables -L -n -v | grep -q "80\|443"; then
    echo "⚠️  iptables has rules that might affect port 80/443"
    echo "   Current rules:"
    sudo iptables -L -n -v | grep -E "80|443" | head -5
    echo ""
    echo "   Do you want to add iptables rules to allow port 80? (y/n)"
    read -p "   " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "   Adding iptables rules..."
        sudo iptables -I INPUT -p tcp --dport 80 -j ACCEPT
        sudo iptables -I INPUT -p tcp --dport 443 -j ACCEPT
        sudo iptables -I INPUT -p tcp --dport 3000 -j ACCEPT
        echo "✅ iptables rules added"
        echo "   Note: These rules will be lost after reboot unless saved"
        echo "   Save with: sudo iptables-save > /etc/iptables/rules.v4"
    fi
else
    echo "✅ No iptables rules blocking port 80/443"
fi
echo ""

# 3. Check if port is actually accessible
echo "3️⃣ Testing port accessibility..."
echo "   Testing from server..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost/ | grep -q "200"; then
    echo "✅ Server can access itself (HTTP 200)"
else
    echo "❌ Server cannot access itself"
fi
echo ""

# 4. Check cloud firewall (informational)
echo "4️⃣ Cloud Firewall Information..."
echo "⚠️  IMPORTANT: If web is still not accessible from outside,"
echo "   the firewall is likely at the cloud provider level (IdCloudHost)."
echo ""
echo "   Since you don't have access to IdCloudHost dashboard, you need to:"
echo "   1. Contact the campus IT/admin who has access"
echo "   2. Ask them to open port 80 (HTTP) in IdCloudHost firewall"
echo "   3. Or provide them with this information:"
echo ""
echo "   📋 Information to give to admin:"
echo "      - Server IP: 103.23.198.101"
echo "      - Port to open: 80 (TCP) for HTTP"
echo "      - Port to open: 443 (TCP) for HTTPS (optional)"
echo "      - Port to open: 3000 (TCP) for SuperAdmin Backend (optional)"
echo ""
echo "   Or you can test if port is open from outside:"
echo "   curl http://103.23.198.101/ (from another computer/network)"
echo ""

# 5. Test external accessibility (if possible)
echo "5️⃣ Testing external accessibility..."
echo "   Attempting to test from server (may not work if firewall blocks)..."
EXTERNAL_TEST=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 5 http://103.23.198.101/ 2>&1)
if [ "$EXTERNAL_TEST" = "200" ]; then
    echo "✅ External access works! (HTTP 200)"
elif [ -z "$EXTERNAL_TEST" ]; then
    echo "⚠️  Cannot test external access (timeout or blocked)"
    echo "   This likely means cloud firewall is blocking"
else
    echo "⚠️  External access returned: HTTP $EXTERNAL_TEST"
fi
echo ""

# 6. Summary
echo "==================================================="
echo "📋 SUMMARY"
echo "==================================================="
echo ""
echo "✅ Server-side checks:"
echo "   - Apache is running"
echo "   - Apache is listening on port 80"
echo "   - Localhost access works"
echo ""
echo "⚠️  Next steps:"
echo "   1. If UFW was enabled, port 80 should be open locally"
echo "   2. Contact campus IT/admin to open port 80 in IdCloudHost firewall"
echo "   3. Test from external computer: http://103.23.198.101/"
echo ""
echo "🧪 Test commands:"
echo "   From server: curl http://localhost/"
echo "   From outside: curl http://103.23.198.101/"
echo "   Or open in browser: http://103.23.198.101/"
echo ""

