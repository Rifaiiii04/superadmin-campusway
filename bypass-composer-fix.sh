#!/bin/bash

echo "🚀 BYPASS COMPOSER FIX"
echo "======================"

# 1. Navigate to directory
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
echo "⏹️ Stopping Apache..."
sudo systemctl stop apache2

# 3. Fix permissions without composer
echo "🔧 Fixing permissions without composer..."

# Remove problematic files
sudo rm -rf storage/logs/laravel.log
sudo rm -rf bootstrap/cache/*

# Create directories
sudo mkdir -p storage/logs
sudo mkdir -p bootstrap/cache

# Set ownership to current user
sudo chown -R $USER:$USER storage bootstrap/cache

# Set permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Create empty log file
touch storage/logs/laravel.log
chmod 664 storage/logs/laravel.log

# 4. Skip composer and run artisan directly
echo "🔧 Running artisan commands directly..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 5. Set final ownership
echo "🔧 Setting final ownership..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 6. Start Apache
echo "🔄 Starting Apache..."
sudo systemctl start apache2

# 7. Test
echo "🧪 Testing..."
curl -s http://103.23.198.101/super-admin/test

echo ""
echo "✅ BYPASS COMPOSER FIX COMPLETE!"
echo "Test: http://103.23.198.101/super-admin/login"
