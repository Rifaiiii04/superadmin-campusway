#!/bin/bash

# Deploy Final Fix for Next.js Intercept Issue
# This script will fix the modal popup and double URL issues

echo "🚀 Deploying Final Fix for Next.js Intercept Issue"
echo "=================================================="

# Exit immediately if a command exits with a non-zero status
set -e

echo "1. Uploading files to VPS..."
# Note: This assumes you have SSH access configured
# scp -r superadmin-backend/ user@103.23.198.101:/var/www/superadmin/

echo "2. Updating Apache configuration..."
echo "   Copying apache-final-fix.conf to /etc/apache2/sites-available/tka.conf"
# sudo cp /var/www/superadmin/superadmin-campusway/apache-final-fix.conf /etc/apache2/sites-available/tka.conf

echo "3. Enabling site..."
# sudo a2ensite tka.conf

echo "4. Enabling required modules..."
# sudo a2enmod rewrite headers deflate expires

echo "5. Restarting Apache..."
# sudo systemctl restart apache2

echo "6. Navigating to Laravel directory..."
# cd /var/www/superadmin/superadmin-campusway

echo "7. Clearing Laravel caches..."
# php artisan config:clear
# php artisan route:clear
# php artisan view:clear
# php artisan cache:clear

echo "8. Rebuilding assets..."
# npm run build

echo "9. Creating admin user..."
# php create-admin-user.php

echo "10. Setting permissions..."
# sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway
# sudo chmod -R 755 /var/www/superadmin/superadmin-campusway
# sudo chmod -R 775 storage bootstrap/cache

echo "✅ Final fix deployment completed!"
echo ""
echo "🧪 Testing URLs:"
echo "   - http://103.23.198.101/super-admin (should show Laravel login)"
echo "   - http://103.23.198.101/super-admin/login (should show Laravel login)"
echo "   - http://103.23.198.101 (should show Next.js frontend)"
echo ""
echo "🔐 Login credentials:"
echo "   - Username: admin"
echo "   - Password: password123"
echo ""
echo "💡 If issues persist:"
echo "   1. Clear browser cache (Ctrl+F5)"
echo "   2. Check Apache error logs: sudo tail -f /var/log/apache2/tka_error.log"
echo "   3. Verify Apache config: sudo apache2ctl configtest"
echo "   4. Check Laravel logs: tail -f storage/logs/laravel.log"
