#!/bin/bash

echo "🚀 Starting SuperAdmin Fix Deployment..."

# 1. Build SuperAdmin with correct base path
echo "📦 Building SuperAdmin with /super-admin/ base path..."
cd /var/www/superadmin/superadmin-campusway
npm run build

# 2. Build Next.js without rewrites
echo "📦 Building Next.js without super-admin rewrites..."
cd /var/www/arahpotensi
npm run build

# 3. Update Apache config
echo "🔧 Updating Apache configuration..."
sudo cp /var/www/superadmin/superadmin-campusway/apache-superadmin-fix.conf /etc/apache2/sites-available/103.23.198.101.conf

# 4. Disable other conflicting sites
echo "🔧 Disabling conflicting Apache sites..."
sudo a2dissite 000-default
sudo a2dissite arahpotensi
sudo a2dissite superadmin
sudo a2dissite tka
sudo a2dissite campusway

# 5. Enable only our site
echo "🔧 Enabling main site..."
sudo a2ensite 103.23.198.101

# 6. Test Apache config
echo "🔧 Testing Apache configuration..."
sudo apache2ctl configtest

# 7. Restart Apache
echo "🔄 Restarting Apache..."
sudo systemctl restart apache2

# 8. Set permissions
echo "🔧 Setting permissions..."
sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway/public/build
sudo chmod -R 755 /var/www/superadmin/superadmin-campusway/public/build

# 9. Clear Laravel cache
echo "🧹 Clearing Laravel cache..."
cd /var/www/superadmin/superadmin-campusway
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "✅ SuperAdmin Fix Deployment Complete!"
echo "🌐 Test: http://103.23.198.101/super-admin/login"
echo "📊 Expected: SuperAdmin login form (not Dashboard Guru)"
