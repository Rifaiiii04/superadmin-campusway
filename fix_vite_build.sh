#!/bin/bash

# Configuration
VPS_USER="root"
VPS_IP="your-vps-ip" # Ganti dengan IP VPS Anda
PROJECT_PATH="/var/www/superadmin/superadmin-campusway" # Ganti dengan path proyek di VPS

echo "🔧 Fixing Vite build issues on VPS..."

# 1. Copy fixed files to VPS
echo "📁 Copying fixed files to VPS..."

# Copy fixed components
scp resources/js/Pages/Sekolah.jsx $VPS_USER@$VPS_IP:$PROJECT_PATH/resources/js/Pages/
scp resources/js/Components/Navigation.jsx $VPS_USER@$VPS_IP:$PROJECT_PATH/resources/js/Components/

if [ $? -ne 0 ]; then
    echo "❌ Error: Failed to copy files to VPS."
    exit 1
fi

echo "✅ Files copied successfully."

# 2. Run build commands on VPS
echo "🔨 Running build commands on VPS..."

ssh $VPS_USER@$VPS_IP "cd $PROJECT_PATH && bash -c '
    echo \"📦 Installing dependencies...\"
    npm install
    
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

echo "🎉 Vite build fix completed successfully!"
echo "🌐 Your application should now build and run properly on the VPS!"
