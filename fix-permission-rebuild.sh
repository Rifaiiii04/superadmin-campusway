#!/bin/bash

echo "🔧 Fixing Permission & Rebuilding SuperAdmin..."

# 1. Set ownership to current user
echo "👤 Setting ownership to current user..."
sudo chown -R $USER:$USER /var/www/superadmin/superadmin-campusway

# 2. Remove build directory completely
echo "🗑️ Removing build directory..."
sudo rm -rf /var/www/superadmin/superadmin-campusway/public/build

# 3. Create build directory with correct permissions
echo "📁 Creating build directory..."
mkdir -p /var/www/superadmin/superadmin-campusway/public/build/assets
chmod -R 755 /var/www/superadmin/superadmin-campusway/public/build

# 4. Build assets
echo "📦 Building assets..."
cd /var/www/superadmin/superadmin-campusway
npm run build

# 5. Set final permissions for web server
echo "🔧 Setting final permissions for web server..."
sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway/public
sudo chmod -R 755 /var/www/superadmin/superadmin-campusway/public

# 6. Test assets
echo "🧪 Testing assets..."
ls -la /var/www/superadmin/superadmin-campusway/public/build/assets/ | head -5

echo "✅ Permission fix & rebuild complete!"
echo "🌐 Test: http://103.23.198.101/super-admin/login"
