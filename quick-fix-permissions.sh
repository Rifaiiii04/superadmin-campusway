#!/bin/bash

echo "🚀 QUICK FIX PERMISSIONS"
echo "========================"

# Navigate to directory
cd /var/www/superadmin/superadmin-campusway

# Quick fix permissions
echo "🔧 Fixing permissions quickly..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Create directories if missing
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Set permissions again
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Clear caches
sudo rm -rf storage/framework/cache/*
sudo rm -rf storage/framework/sessions/*
sudo rm -rf storage/framework/views/*
sudo rm -rf bootstrap/cache/*

# Set permissions after clearing
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Test composer
echo "📦 Testing composer install..."
composer install --no-dev --optimize-autoloader

echo "✅ Quick fix complete!"
echo "Test: curl http://103.23.198.101/super-admin/test"
