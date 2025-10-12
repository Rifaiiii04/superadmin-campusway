#!/bin/bash

# Configuration
VPS_USER="root"
VPS_IP="your-vps-ip" # Ganti dengan IP VPS Anda
PROJECT_PATH="/var/www/superadmin/superadmin-campusway" # Ganti dengan path proyek di VPS

echo "🔧 Fixing MajorRecommendations on VPS..."

# 1. Copy fixed files to VPS
echo "📁 Copying fixed files to VPS..."

# Copy controller
scp app/Http/Controllers/MajorRecommendationController.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Http/Controllers/

# Copy test script
scp test_vps_major_recommendations.php $VPS_USER@$VPS_IP:$PROJECT_PATH/

if [ $? -ne 0 ]; then
    echo "❌ Error: Failed to copy files to VPS."
    exit 1
fi

echo "✅ Files copied successfully."

# 2. Run test and build on VPS
echo "🔨 Running test and build on VPS..."

ssh $VPS_USER@$VPS_IP "cd $PROJECT_PATH && bash -c '
    echo \"🧪 Testing MajorRecommendations controller...\"
    php test_vps_major_recommendations.php
    
    echo \"🔨 Building frontend assets...\"
    npm run build
    
    echo \"🧹 Clearing caches...\"
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
    
    echo \"🔧 Optimizing application...\"
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    echo \"🔄 Restarting services...\"
    systemctl restart apache2
    systemctl restart php8.1-fpm
    
    echo \"✅ Build completed successfully.\"
'"

if [ $? -ne 0 ]; then
    echo "❌ Error: Failed to execute commands on VPS."
    exit 1
fi

echo "🎉 MajorRecommendations fix completed successfully!"
echo "🌐 Check your VPS URL to see if the data is now showing!"
