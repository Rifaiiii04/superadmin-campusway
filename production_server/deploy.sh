#!/bin/bash
echo "🚀 Deploying to production server..."

# Stop any running processes
sudo systemctl stop apache2 2>/dev/null || true
sudo systemctl stop nginx 2>/dev/null || true

# Backup current build
sudo cp -r /var/www/html/public/build /var/www/html/public/build.backup.$(date +%Y%m%d_%H%M%S) 2>/dev/null || true

# Copy new assets
sudo cp -r assets/* /var/www/html/public/build/assets/
sudo cp manifest.json /var/www/html/public/build/

# Set permissions
sudo chown -R www-data:www-data /var/www/html/public/build/
sudo chmod -R 755 /var/www/html/public/build/

# Clear Laravel caches
cd /var/www/html
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan route:clear

# Restart services
sudo systemctl start apache2 2>/dev/null || true
sudo systemctl start nginx 2>/dev/null || true

echo "✅ Deployment complete!"
echo "🌐 Test your website now"
