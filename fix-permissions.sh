#!/bin/bash

echo "🔧 FIXING PERMISSION ISSUES"
echo "============================"

# 1. Navigate to the superadmin directory
echo "📁 Step 1: Navigating to /var/www/superadmin/superadmin-campusway..."
cd /var/www/superadmin/superadmin-campusway || { echo "❌ Failed to navigate to superadmin-campusway. Exiting."; exit 1; }

# 2. Stop Apache temporarily
echo "⏹️ Step 2: Stopping Apache temporarily..."
sudo systemctl stop apache2

# 3. Fix ownership
echo "🔧 Step 3: Fixing ownership..."
sudo chown -R www-data:www-data .
sudo chown -R $USER:$USER .

# 4. Create necessary directories
echo "📁 Step 4: Creating necessary directories..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache
mkdir -p public/build

# 5. Set proper permissions
echo "🔧 Step 5: Setting proper permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
sudo chmod -R 755 public
sudo chmod -R 755 public/build

# 6. Clear all caches
echo "🧹 Step 6: Clearing all caches..."
sudo rm -rf storage/framework/cache/*
sudo rm -rf storage/framework/sessions/*
sudo rm -rf storage/framework/views/*
sudo rm -rf bootstrap/cache/*

# 7. Set permissions again after clearing
echo "🔧 Step 7: Setting permissions again..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 8. Test permissions
echo "🧪 Step 8: Testing permissions..."
touch storage/logs/laravel.log 2>/dev/null && echo "✅ storage/logs/laravel.log writable" || echo "❌ storage/logs/laravel.log not writable"
touch bootstrap/cache/packages.php 2>/dev/null && echo "✅ bootstrap/cache writable" || echo "❌ bootstrap/cache not writable"

# 9. Install composer dependencies
echo "📦 Step 9: Installing composer dependencies..."
composer install --no-dev --optimize-autoloader || { echo "❌ Composer install failed. Exiting."; exit 1; }

# 10. Clear Laravel caches
echo "🧹 Step 10: Clearing Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize:clear

# 11. Set final permissions
echo "🔧 Step 11: Setting final permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache public
sudo chmod -R 775 storage bootstrap/cache
sudo chmod -R 755 public

# 12. Start Apache
echo "🔄 Step 12: Starting Apache..."
sudo systemctl start apache2

# 13. Test the application
echo "🧪 Step 13: Testing the application..."
echo ""
echo "Testing SuperAdmin test route:"
curl -s http://103.23.198.101/super-admin/test || echo "❌ SuperAdmin test failed"
echo ""

echo "Testing SuperAdmin login page:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ SuperAdmin login failed"
echo ""

echo "✅ PERMISSION FIX COMPLETE!"
echo "============================"
echo "🌐 Test the application at: http://103.23.198.101/super-admin/login"
echo "👤 Login credentials:"
echo "   Username: admin"
echo "   Password: password123"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Fixed storage/logs/laravel.log permissions"
echo "   ✅ Fixed bootstrap/cache permissions"
echo "   ✅ Created all necessary directories"
echo "   ✅ Set proper ownership (www-data:www-data)"
echo "   ✅ Set proper permissions (775 for storage, 755 for public)"
echo "   ✅ Cleared all caches"
echo "   ✅ Reinstalled composer dependencies"
echo ""
echo "🎉 Permission issues should now be resolved!"
