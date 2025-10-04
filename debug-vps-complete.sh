#!/bin/bash

echo "🔍 COMPLETE VPS DEBUG"
echo "===================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Check current directory
echo "📋 Step 1: Current directory and files..."
echo "=== CURRENT DIRECTORY ==="
pwd
ls -la
echo ""

# 3. Check Apache status
echo "📋 Step 2: Apache status..."
echo "=== APACHE STATUS ==="
sudo systemctl status apache2 --no-pager
echo ""

# 4. Check Apache config
echo "📋 Step 3: Apache configuration..."
echo "=== APACHE CONFIG ==="
sudo cat /etc/apache2/sites-available/103.23.198.101.conf
echo ""

# 5. Check enabled sites
echo "📋 Step 4: Enabled sites..."
echo "=== ENABLED SITES ==="
sudo a2ensite -l
echo ""

# 6. Check Apache virtual hosts
echo "📋 Step 5: Apache virtual hosts..."
echo "=== VIRTUAL HOSTS ==="
sudo apache2ctl -S
echo ""

# 7. Check DocumentRoot
echo "📋 Step 6: DocumentRoot..."
echo "=== DOCUMENT ROOT ==="
apache2ctl -S | grep DocumentRoot
echo ""

# 8. Check if super-admin directory exists
echo "📋 Step 7: Super-admin directory..."
echo "=== SUPER-ADMIN DIRECTORY ==="
ls -la /var/www/superadmin/
ls -la /var/www/superadmin/superadmin-campusway/
echo ""

# 9. Check public directory
echo "📋 Step 8: Public directory..."
echo "=== PUBLIC DIRECTORY ==="
ls -la /var/www/superadmin/superadmin-campusway/public/
echo ""

# 10. Check build directory
echo "📋 Step 9: Build directory..."
echo "=== BUILD DIRECTORY ==="
ls -la /var/www/superadmin/superadmin-campusway/public/build/
echo ""

# 11. Check assets directory
echo "📋 Step 10: Assets directory..."
echo "=== ASSETS DIRECTORY ==="
ls -la /var/www/superadmin/superadmin-campusway/public/build/assets/
echo ""

# 12. Check manifest.json
echo "📋 Step 11: Manifest.json..."
echo "=== MANIFEST.JSON ==="
if [ -f "/var/www/superadmin/superadmin-campusway/public/build/manifest.json" ]; then
    cat /var/www/superadmin/superadmin-campusway/public/build/manifest.json | head -20
else
    echo "❌ manifest.json not found"
fi
echo ""

# 13. Check app.blade.php
echo "📋 Step 12: App.blade.php..."
echo "=== APP.BLADE.PHP ==="
cat /var/www/superadmin/superadmin-campusway/resources/views/app.blade.php
echo ""

# 14. Check routes
echo "📋 Step 13: Routes..."
echo "=== ROUTES ==="
php artisan route:list | head -20
echo ""

# 15. Check Laravel logs
echo "📋 Step 14: Laravel logs..."
echo "=== LARAVEL LOGS ==="
tail -20 /var/www/superadmin/superadmin-campusway/storage/logs/laravel.log 2>/dev/null || echo "❌ Laravel log not found"
echo ""

# 16. Check Apache error log
echo "📋 Step 15: Apache error log..."
echo "=== APACHE ERROR LOG ==="
sudo tail -20 /var/log/apache2/error.log
echo ""

# 17. Check Apache access log
echo "📋 Step 16: Apache access log..."
echo "=== APACHE ACCESS LOG ==="
sudo tail -20 /var/log/apache2/103.23.198.101_access.log 2>/dev/null || echo "❌ Access log not found"
echo ""

# 18. Test direct access
echo "📋 Step 17: Testing direct access..."
echo "=== DIRECT ACCESS ==="
curl -I http://103.23.198.101/super-admin/ 2>/dev/null || echo "❌ SuperAdmin root failed"
curl -I http://103.23.198.101/super-admin/login 2>/dev/null || echo "❌ SuperAdmin login failed"
curl -I http://103.23.198.101/super-admin/test 2>/dev/null || echo "❌ SuperAdmin test failed"
echo ""

# 19. Check file permissions
echo "📋 Step 18: File permissions..."
echo "=== FILE PERMISSIONS ==="
ls -la /var/www/superadmin/superadmin-campusway/public/
ls -la /var/www/superadmin/superadmin-campusway/public/build/
echo ""

# 20. Check PHP version
echo "📋 Step 19: PHP version..."
echo "=== PHP VERSION ==="
php --version
echo ""

# 21. Check if .env exists
echo "📋 Step 20: Environment file..."
echo "=== .ENV FILE ==="
if [ -f "/var/www/superadmin/superadmin-campusway/.env" ]; then
    echo "✅ .env file exists"
    grep -E "APP_ENV|APP_DEBUG|APP_URL" /var/www/superadmin/superadmin-campusway/.env
else
    echo "❌ .env file not found"
fi
echo ""

echo "✅ COMPLETE VPS DEBUG COMPLETE!"
echo "==============================="
echo "Check the output above for issues."
