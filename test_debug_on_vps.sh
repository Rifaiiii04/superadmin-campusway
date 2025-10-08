#!/bin/bash

echo "=== TESTING DEBUG ON VPS AFTER PULL ==="

# Set VPS details
VPS_USER="marketing"
VPS_HOST="103.23.198.101"
VPS_PATH="/var/www/superadmin/superadmin-campusway"

echo "1. Pulling latest changes from GitHub..."
ssh $VPS_USER@$VPS_HOST "cd $VPS_PATH && git pull origin main"

echo "2. Building frontend..."
ssh $VPS_USER@$VPS_HOST "cd $VPS_PATH && npm run build"

echo "3. Clearing caches..."
ssh $VPS_USER@$VPS_HOST "cd $VPS_PATH && php artisan config:cache && php artisan route:cache && php artisan view:cache"

echo "4. Setting permissions..."
ssh $VPS_USER@$VPS_HOST "cd $VPS_PATH && sudo chown -R www-data:www-data storage bootstrap/cache && sudo chmod -R 775 storage bootstrap/cache"

echo "5. Restarting Apache..."
ssh $VPS_USER@$VPS_HOST "sudo systemctl restart apache2"

echo "6. Testing all controllers..."
ssh $VPS_USER@$VPS_HOST "cd $VPS_PATH && php test_all_controllers_vps.php"

echo "7. Checking Laravel logs..."
ssh $VPS_USER@$VPS_HOST "cd $VPS_PATH && tail -20 storage/logs/laravel.log"

echo "✅ Debug testing completed!"
echo.
echo "Now you can:"
echo "1. Open browser and go to SuperAdmin pages"
echo "2. Check browser console for debug logs"
echo "3. Check Laravel logs: tail -f storage/logs/laravel.log"
