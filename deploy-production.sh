#!/bin/bash

# Production Deployment Script for TKA Super Admin
# Run this script on your VPS

echo "🚀 Starting TKA Super Admin Production Deployment..."

# Set production environment
export NODE_ENV=production

# Navigate to project directory
cd /var/www/superadmin/superadmin-campusway

# Pull latest changes
echo "📥 Pulling latest changes..."
git pull origin main

# Install/Update PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Install/Update Node dependencies
echo "📦 Installing Node dependencies..."
npm ci --production

# Build assets for production
echo "🔨 Building assets for production..."
npm run build

# Clear Laravel caches
echo "🧹 Clearing Laravel caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Set proper permissions
echo "🔐 Setting proper permissions..."
sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway
sudo chmod -R 755 /var/www/superadmin/superadmin-campusway
sudo chmod -R 775 /var/www/superadmin/superadmin-campusway/storage
sudo chmod -R 775 /var/www/superadmin/superadmin-campusway/bootstrap/cache

# Restart Apache
echo "🔄 Restarting Apache..."
sudo systemctl restart apache2

# Restart PHP-FPM
echo "🔄 Restarting PHP-FPM..."
sudo systemctl restart php8.3-fpm

echo "✅ Production deployment completed!"
echo "🌐 Application available at: http://103.23.198.101/super-admin"
