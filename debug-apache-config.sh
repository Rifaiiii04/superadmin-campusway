#!/bin/bash

echo "🔍 DEBUGGING APACHE CONFIG"
echo "========================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Check current Apache config
echo "📋 Step 1: Checking current Apache config..."
echo "=== APACHE CONFIG ==="
sudo cat /etc/apache2/sites-available/000-default.conf
echo ""

# 3. Check enabled sites
echo "📋 Step 2: Checking enabled sites..."
echo "=== ENABLED SITES ==="
sudo a2ensite -l
echo ""

# 4. Check Apache status
echo "📋 Step 3: Checking Apache status..."
echo "=== APACHE STATUS ==="
sudo systemctl status apache2 --no-pager
echo ""

# 5. Check DocumentRoot
echo "📋 Step 4: Checking DocumentRoot..."
echo "=== DOCUMENT ROOT ==="
apache2ctl -S | grep DocumentRoot
echo ""

# 6. Check if build directory exists
echo "📋 Step 5: Checking build directory..."
echo "=== BUILD DIRECTORY ==="
ls -la public/build/
echo ""

# 7. Check if assets exist
echo "📋 Step 6: Checking assets..."
echo "=== ASSETS ==="
ls -la public/build/assets/ | head -10
echo ""

# 8. Check specific assets
echo "📋 Step 7: Checking specific assets..."
echo "=== SPECIFIC ASSETS ==="
ls -la public/build/assets/app-DWYINBfv.js 2>/dev/null || echo "❌ app-DWYINBfv.js not found"
ls -la public/build/assets/app-d2W3ZRG7.css 2>/dev/null || echo "❌ app-d2W3ZRG7.css not found"
echo ""

# 9. Check manifest.json
echo "📋 Step 8: Checking manifest.json..."
echo "=== MANIFEST ==="
grep -A 5 -B 5 "resources/js/app.jsx" public/build/manifest.json
echo ""

# 10. Test direct access
echo "📋 Step 9: Testing direct access..."
echo "=== DIRECT ACCESS ==="
curl -I http://103.23.198.101/super-admin/ 2>/dev/null || echo "❌ SuperAdmin root failed"
curl -I http://103.23.198.101/super-admin/login 2>/dev/null || echo "❌ SuperAdmin login failed"
curl -I http://103.23.198.101/super-admin/build/ 2>/dev/null || echo "❌ Build directory failed"
echo ""

# 11. Check Apache error log
echo "📋 Step 10: Checking Apache error log..."
echo "=== APACHE ERROR LOG ==="
sudo tail -20 /var/log/apache2/error.log
echo ""

echo "✅ DEBUG COMPLETE!"
echo "Check the output above for issues."
