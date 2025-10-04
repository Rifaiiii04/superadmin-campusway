#!/bin/bash

echo "⚡ QUICK FIX FOR 500 ERROR"
echo "========================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
sudo systemctl stop apache2

# 3. Quick permission fix
echo "🔧 Quick permission fix..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 4. Clear caches
echo "🧹 Clearing caches..."
sudo rm -rf storage/framework/cache/*
sudo rm -rf storage/framework/sessions/*
sudo rm -rf storage/framework/views/*
sudo rm -rf bootstrap/cache/*

# 5. Create log file
touch storage/logs/laravel.log
chmod 664 storage/logs/laravel.log

# 6. Run artisan
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 7. Start Apache
sudo systemctl start apache2

# 8. Test
echo "🧪 Testing..."
curl -s http://103.23.198.101/super-admin/test || echo "❌ Test failed"
curl -I http://103.23.198.101/super-admin/login || echo "❌ Login failed"

echo ""
echo "✅ QUICK FIX COMPLETE!"
echo "Test: http://103.23.198.101/super-admin/login"
