#!/bin/bash

# Configuration
VPS_USER="root"
VPS_IP="your-vps-ip" # Ganti dengan IP VPS Anda
PROJECT_PATH="/var/www/superadmin/superadmin-campusway" # Ganti dengan path proyek di VPS

echo "🔧 Fixing MajorRecommendations filter error on VPS..."

# 1. Copy fixed file to VPS
echo "📁 Copying fixed MajorRecommendations.jsx to VPS..."

scp resources/js/Pages/SuperAdmin/MajorRecommendations.jsx $VPS_USER@$VPS_IP:$PROJECT_PATH/resources/js/Pages/SuperAdmin/

if [ $? -ne 0 ]; then
    echo "❌ Error: Failed to copy file to VPS."
    exit 1
fi

echo "✅ File copied successfully."

# 2. Run build commands on VPS
echo "🔨 Running build commands on VPS..."

ssh $VPS_USER@$VPS_IP "cd $PROJECT_PATH && bash -c '
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
    echo "❌ Error: Failed to execute build commands on VPS."
    exit 1
fi

echo "🎉 MajorRecommendations filter fix completed successfully!"
echo "🌐 Your application should now work without filter errors!"
