#!/bin/bash

# Configuration
VPS_USER="root"
VPS_IP="your-vps-ip" # Ganti dengan IP VPS Anda
PROJECT_PATH="/var/www/superadmin/superadmin-campusway" # Ganti dengan path proyek di VPS

echo "🔧 Deploying MajorRecommendations fix to VPS..."

# 1. Copy fixed files to VPS
echo "📁 Copying fixed files to VPS..."

# Copy controller
scp app/Http/Controllers/MajorRecommendationController.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Http/Controllers/

# Copy frontend component
scp resources/js/Pages/SuperAdmin/MajorRecommendations.jsx $VPS_USER@$VPS_IP:$PROJECT_PATH/resources/js/Pages/SuperAdmin/

if [ $? -ne 0 ]; then
    echo "❌ Error: Failed to copy files to VPS."
    exit 1
fi

echo "✅ Files copied successfully."

# 2. Run build and restart on VPS
echo "🔨 Running build and restart on VPS..."

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
    echo "❌ Error: Failed to execute commands on VPS."
    exit 1
fi

echo "🎉 MajorRecommendations fix deployed successfully!"
echo "🌐 Check your VPS URL to see if the data is now showing!"
echo "🔍 Check browser console for debug information!"
