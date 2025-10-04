#!/bin/bash

echo "🔧 Fixing SuperAdmin Assets on VPS..."

# 1. Set permissions
echo "📁 Setting permissions..."
sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway/public
sudo chmod -R 755 /var/www/superadmin/superadmin-campusway/public

# 2. Remove old build directory
echo "🗑️ Removing old build directory..."
sudo rm -rf /var/www/superadmin/superadmin-campusway/public/build

# 3. Create new build directory
echo "📁 Creating new build directory..."
sudo mkdir -p /var/www/superadmin/superadmin-campusway/public/build/assets
sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway/public/build
sudo chmod -R 755 /var/www/superadmin/superadmin-campusway/public/build

# 4. Build assets on VPS
echo "📦 Building assets on VPS..."
cd /var/www/superadmin/superadmin-campusway
npm run build

# 5. Set final permissions
echo "🔧 Setting final permissions..."
sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway/public/build
sudo chmod -R 755 /var/www/superadmin/superadmin-campusway/public/build

# 6. Test assets
echo "🧪 Testing assets..."
ls -la /var/www/superadmin/superadmin-campusway/public/build/assets/ | head -5

echo "✅ Assets fix complete!"
echo "🌐 Test: http://103.23.198.101/super-admin/login"
