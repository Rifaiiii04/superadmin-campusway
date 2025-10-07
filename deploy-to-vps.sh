#!/bin/bash

echo "🚀 Deploying SuperAdmin CampusWay to VPS..."

# Set source and destination paths
SOURCE_DIR="/home/raihan-yasykur/superadmin-campusway-deploy"
DEST_DIR="/var/www/superadmin/superadmin-campusway"

echo "📁 Source: $SOURCE_DIR"
echo "📁 Destination: $DEST_DIR"

# Create destination directory if it doesn't exist
echo "📂 Creating destination directory..."
sudo mkdir -p $DEST_DIR

# Copy files to VPS directory
echo "📋 Copying files..."
sudo rsync -avz --delete $SOURCE_DIR/ $DEST_DIR/

# Set proper permissions
echo "🔐 Setting permissions..."
sudo chown -R www-data:www-data $DEST_DIR
sudo chmod -R 755 $DEST_DIR

# Set special permissions for storage and cache
echo "🔧 Setting special permissions..."
sudo chmod -R 775 $DEST_DIR/storage
sudo chmod -R 775 $DEST_DIR/bootstrap/cache

# Install dependencies
echo "📦 Installing dependencies..."
cd $DEST_DIR
sudo composer install --no-dev --optimize-autoloader

# Build frontend assets
echo "🎨 Building frontend assets..."
sudo npm install
sudo npm run build

# Clear Laravel caches
echo "🧹 Clearing caches..."
sudo php artisan config:clear
sudo php artisan route:clear
sudo php artisan view:clear
sudo php artisan cache:clear

# Generate application key if not exists
echo "🔑 Generating application key..."
sudo php artisan key:generate

# Run migrations
echo "🗄️ Running migrations..."
sudo php artisan migrate --force

# Restart web server
echo "🔄 Restarting web server..."
sudo systemctl restart apache2

echo "✅ Deployment completed successfully!"
echo "🌐 Your application should be available at your VPS domain"