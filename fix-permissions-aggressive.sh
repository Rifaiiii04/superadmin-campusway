#!/bin/bash

echo "🔥 AGGRESSIVE PERMISSION FIX"
echo "============================"

# 1. Navigate to directory
echo "📁 Step 1: Navigating to /var/www/superadmin/superadmin-campusway..."
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
echo "⏹️ Step 2: Stopping Apache..."
sudo systemctl stop apache2

# 3. Remove problematic files/directories
echo "🗑️ Step 3: Removing problematic files..."
sudo rm -rf storage/logs/laravel.log
sudo rm -rf bootstrap/cache/*
sudo rm -rf storage/framework/cache/*
sudo rm -rf storage/framework/sessions/*
sudo rm -rf storage/framework/views/*

# 4. Create directories with proper ownership
echo "📁 Step 4: Creating directories with proper ownership..."
sudo mkdir -p storage/logs
sudo mkdir -p storage/framework/cache
sudo mkdir -p storage/framework/sessions
sudo mkdir -p storage/framework/views
sudo mkdir -p bootstrap/cache

# 5. Set ownership to current user first
echo "🔧 Step 5: Setting ownership to current user..."
sudo chown -R $USER:$USER storage bootstrap/cache

# 6. Set permissions
echo "🔧 Step 6: Setting permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# 7. Create empty log file
echo "📄 Step 7: Creating empty log file..."
touch storage/logs/laravel.log
chmod 664 storage/logs/laravel.log

# 8. Test permissions
echo "🧪 Step 8: Testing permissions..."
echo "Testing storage/logs/laravel.log..."
if [ -w "storage/logs/laravel.log" ]; then
    echo "✅ storage/logs/laravel.log is writable"
else
    echo "❌ storage/logs/laravel.log is NOT writable"
fi

echo "Testing bootstrap/cache..."
if [ -w "bootstrap/cache" ]; then
    echo "✅ bootstrap/cache is writable"
else
    echo "❌ bootstrap/cache is NOT writable"
fi

# 9. Try composer install
echo "📦 Step 9: Trying composer install..."
composer install --no-dev --optimize-autoloader --no-scripts

# 10. Run artisan commands manually
echo "🔧 Step 10: Running artisan commands manually..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 11. Set final ownership to www-data
echo "🔧 Step 11: Setting final ownership to www-data..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 12. Start Apache
echo "🔄 Step 12: Starting Apache..."
sudo systemctl start apache2

# 13. Test
echo "🧪 Step 13: Testing..."
echo "Testing SuperAdmin test route:"
curl -s http://103.23.198.101/super-admin/test || echo "❌ SuperAdmin test failed"

echo ""
echo "Testing SuperAdmin login page:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ SuperAdmin login failed"

echo ""
echo "✅ AGGRESSIVE PERMISSION FIX COMPLETE!"
echo "============================"
echo "🌐 Test the application at: http://103.23.198.101/super-admin/login"
