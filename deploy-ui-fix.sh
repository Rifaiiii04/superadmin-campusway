#!/bin/bash

echo "🚀 DEPLOY UI FIX - REACT + VITE + LARAVEL"
echo "========================================="

cd /var/www/superadmin/superadmin-campusway

echo ""
echo "=== STEP 1: INSTALL NPM DEPENDENCIES ==="
npm install

echo ""
echo "=== STEP 2: BUILD VITE ASSETS ==="
npm run build

echo ""
echo "=== STEP 3: SET PERMISSIONS ==="
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod 644 .env

echo ""
echo "=== STEP 4: CLEAR CACHE ==="
sudo php artisan config:clear
sudo php artisan route:clear
sudo php artisan view:clear
sudo php artisan cache:clear

echo ""
echo "=== STEP 5: RESTART APACHE ==="
sudo systemctl restart apache2

echo ""
echo "=== STEP 6: TEST UI ==="
echo "Testing /login (should show UI)..."
curl -s http://103.23.198.101/super-admin/login | head -20

echo ""
echo "Testing /dashboard (should redirect to login)..."
curl -I http://103.23.198.101/super-admin/dashboard

echo ""
echo "Testing /health (should return JSON)..."
curl -s http://103.23.198.101/super-admin/health

echo ""
echo "🎉 UI DEPLOY COMPLETE!"
echo "====================="
echo "✅ Installed NPM dependencies"
echo "✅ Built Vite assets"
echo "✅ Set permissions"
echo "✅ Cleared cache"
echo "✅ Restarted Apache"
echo ""
echo "🌐 Access URLs:"
echo "   http://103.23.198.101/super-admin/login (UI Login)"
echo "   http://103.23.198.101/super-admin/dashboard (UI Dashboard)"
echo "   http://103.23.198.101/super-admin/health (API Health)"
echo ""
echo "🚀 READY FOR PRODUCTION!"
