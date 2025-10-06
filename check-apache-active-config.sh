#!/bin/bash

echo "🔍 CHECKING ACTIVE APACHE CONFIG"
echo "==============================="

# 1. Check which config is active
echo "📋 Step 1: Checking active Apache config..."
echo "=== ACTIVE CONFIG ==="
sudo apache2ctl -S
echo ""

# 2. Check DocumentRoot
echo "📋 Step 2: Checking DocumentRoot..."
echo "=== DOCUMENT ROOT ==="
apache2ctl -S | grep DocumentRoot
echo ""

# 3. Check VirtualHosts
echo "📋 Step 3: Checking VirtualHosts..."
echo "=== VIRTUAL HOSTS ==="
apache2ctl -S | grep -A 10 -B 5 "103.23.198.101"
echo ""

# 4. Check if super-admin is accessible
echo "📋 Step 4: Testing super-admin access..."
echo "=== SUPER-ADMIN ACCESS ==="
curl -I http://103.23.198.101/super-admin/ 2>/dev/null || echo "❌ SuperAdmin root failed"
curl -I http://103.23.198.101/super-admin/login 2>/dev/null || echo "❌ SuperAdmin login failed"
echo ""

# 5. Check if build directory is accessible
echo "📋 Step 5: Testing build directory access..."
echo "=== BUILD DIRECTORY ACCESS ==="
curl -I http://103.23.198.101/super-admin/build/ 2>/dev/null || echo "❌ Build directory failed"
curl -I http://103.23.198.101/super-admin/build/assets/ 2>/dev/null || echo "❌ Assets directory failed"
echo ""

# 6. Check if assets exist on filesystem
echo "📋 Step 6: Checking assets on filesystem..."
echo "=== ASSETS ON FILESYSTEM ==="
cd /var/www/superadmin/superadmin-campusway
ls -la public/build/assets/app-DWYINBfv.js 2>/dev/null || echo "❌ app-DWYINBfv.js not found on filesystem"
ls -la public/build/assets/app-d2W3ZRG7.css 2>/dev/null || echo "❌ app-d2W3ZRG7.css not found on filesystem"
echo ""

# 7. Check Apache access log
echo "📋 Step 7: Checking Apache access log..."
echo "=== APACHE ACCESS LOG ==="
sudo tail -10 /var/log/apache2/103.23.198.101_access.log 2>/dev/null || echo "❌ Access log not found"
echo ""

# 8. Check Apache error log
echo "📋 Step 8: Checking Apache error log..."
echo "=== APACHE ERROR LOG ==="
sudo tail -10 /var/log/apache2/error.log 2>/dev/null || echo "❌ Error log not found"
echo ""

echo "✅ DEBUG COMPLETE!"
echo "Check the output above for issues."
