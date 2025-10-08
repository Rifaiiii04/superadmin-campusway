#!/bin/bash

echo "=== DEPLOYING ALL CONTROLLERS FIX TO VPS ==="

# Set VPS details
VPS_USER="marketing"
VPS_HOST="103.23.198.101"
VPS_PATH="/var/www/superadmin/superadmin-campusway"

echo "1. Copying files to VPS..."

# Copy all controller files
scp app/Http/Controllers/SchoolController.php $VPS_USER@$VPS_HOST:$VPS_PATH/app/Http/Controllers/
scp app/Http/Controllers/MajorRecommendationController.php $VPS_USER@$VPS_HOST:$VPS_PATH/app/Http/Controllers/
scp app/Http/Controllers/StudentController.php $VPS_USER@$VPS_HOST:$VPS_PATH/app/Http/Controllers/
scp app/Http/Controllers/QuestionController.php $VPS_USER@$VPS_HOST:$VPS_PATH/app/Http/Controllers/
scp app/Http/Controllers/ResultController.php $VPS_USER@$VPS_HOST:$VPS_PATH/app/Http/Controllers/
scp app/Http/Controllers/SuperAdmin/TkaScheduleController.php $VPS_USER@$VPS_HOST:$VPS_PATH/app/Http/Controllers/SuperAdmin/

# Copy routes file
scp routes/web.php $VPS_USER@$VPS_HOST:$VPS_PATH/routes/

# Copy test script
scp test_all_controllers_vps.php $VPS_USER@$VPS_HOST:$VPS_PATH/

echo "2. Running commands on VPS..."

# Run commands on VPS
ssh $VPS_USER@$VPS_HOST << 'EOF'
cd /var/www/superadmin/superadmin-campusway

echo "Clearing caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Building frontend..."
npm run build

echo "Setting permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

echo "Restarting Apache..."
sudo systemctl restart apache2

echo "Testing controllers..."
php test_all_controllers_vps.php

echo "Deployment completed!"
EOF

echo "✅ All controllers fix deployed successfully!"
