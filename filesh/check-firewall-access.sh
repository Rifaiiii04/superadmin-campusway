#!/bin/bash

echo "🔍 Checking Firewall and Network Access"
echo "========================================"
echo ""

# 1. Test from server itself
echo "1️⃣ Testing from server (localhost)..."
curl -I http://localhost/ 2>&1 | head -1
echo ""

# 2. Test from server using external IP
echo "2️⃣ Testing from server using external IP..."
curl -I http://103.23.198.101/ 2>&1 | head -1
echo ""

# 3. Check if iptables is blocking
echo "3️⃣ Checking iptables rules..."
sudo iptables -L -n -v | grep -E "80|443" || echo "No iptables rules for port 80/443"
echo ""

# 4. Check listening ports
echo "4️⃣ Checking listening ports..."
sudo netstat -tlnp | grep -E ":80|:443|:3000"
echo ""

# 5. Check Apache access log for external connections
echo "5️⃣ Recent Apache access log (last 10 lines)..."
sudo tail -10 /var/log/apache2/103.23.198.101_access.log 2>/dev/null || echo "No access log found"
echo ""

# 6. Check if there's a firewall service
echo "6️⃣ Checking for firewall services..."
systemctl status ufw 2>&1 | grep -E "Active|Status" || echo "ufw not found"
echo ""

echo "========================================"
echo "📋 Summary:"
echo "  - If localhost works but external IP doesn't, it's likely a cloud firewall issue"
echo "  - Check IdCloudHost dashboard for firewall rules"
echo "  - Make sure port 80 and 443 are open in cloud firewall"
echo ""

