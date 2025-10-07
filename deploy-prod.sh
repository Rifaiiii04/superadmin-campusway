#!/bin/bash
cd /var/www/superadmin/superadmin-campusway

echo "=== PRODUCTION DEPLOYMENT STARTED ==="

# Stop any running dev servers
pkill -f "vite" 2>/dev/null || true

# Fix permissions for build
sudo chown -R marketing:marketing .
chmod -R 755 .
chmod -R 775 storage bootstrap/cache

# Install dependencies if needed
npm install

# Build production assets
echo "Building production assets..."
npm run build

# Fix ownership for web server
sudo chown -R www-data:www-data public/build/
sudo chown -R www-data:www-data storage/ bootstrap/cache/

# Clear Laravel caches
sudo php artisan config:clear
sudo php artisan cache:clear  
sudo php artisan route:clear
sudo php artisan view:clear

# Restart web server
sudo systemctl restart apache2

echo "=== PRODUCTION DEPLOYMENT COMPLETED ==="
echo "Application ready at: http://103.23.198.101/super-admin/login"
