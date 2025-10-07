#!/bin/bash

echo "🚀 Deploying SuperAdmin CampusWay (Local Test)..."

# Set source and destination paths
SOURCE_DIR="/home/raihan-yasykur/superadmin-campusway-deploy"
DEST_DIR="/home/raihan-yasykur/vps-deploy-test"

echo "📁 Source: $SOURCE_DIR"
echo "📁 Destination: $DEST_DIR"

# Create destination directory if it doesn't exist
echo "📂 Creating destination directory..."
mkdir -p $DEST_DIR

# Copy files to test directory
echo "📋 Copying files..."
rsync -avz --delete $SOURCE_DIR/ $DEST_DIR/

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 $DEST_DIR

# Set special permissions for storage and cache
echo "🔧 Setting special permissions..."
chmod -R 775 $DEST_DIR/storage
chmod -R 775 $DEST_DIR/bootstrap/cache

# Install dependencies
echo "📦 Installing dependencies..."
cd $DEST_DIR
composer install --no-dev --optimize-autoloader

# Build frontend assets
echo "🎨 Building frontend assets..."
npm install
npm run build

# Clear Laravel caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Generate application key if not exists
echo "🔑 Generating application key..."
php artisan key:generate

# Run migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

echo "✅ Local deployment test completed successfully!"
echo "🌐 Test directory: $DEST_DIR"
echo "📝 To deploy to VPS, run: sudo ./deploy-to-vps.sh"
