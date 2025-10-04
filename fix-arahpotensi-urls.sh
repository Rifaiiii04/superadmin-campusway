#!/bin/bash

echo "🔧 FIXING ARAHPOTENSI HARDCODED URLS"
echo "===================================="

# 1. Navigate to arahpotensi directory
echo "📁 Step 1: Navigating to /var/www/arahpotensi..."
cd /var/www/arahpotensi || { echo "❌ Failed to navigate to arahpotensi. Exiting."; exit 1; }

# 2. Pull latest changes
echo "📥 Step 2: Pulling latest changes from Git..."
git pull origin main || { echo "❌ Git pull failed. Exiting."; exit 1; }

# 3. Fix all hardcoded URLs
echo "🔧 Step 3: Fixing all hardcoded URLs..."
find src -name "*.tsx" -o -name "*.ts" -o -name "*.js" -o -name "*.jsx" | xargs sed -i 's/127\.0\.0\.1:8000/103.23.198.101\/super-admin/g'

# 4. Build the application
echo "🔨 Step 4: Building ArahPotensi..."
npm run build || { echo "❌ Build failed. Exiting."; exit 1; }

# 5. Set permissions
echo "🔧 Step 5: Setting permissions..."
sudo chown -R www-data:www-data out
sudo chmod -R 755 out

# 6. Test the application
echo "🧪 Step 6: Testing ArahPotensi..."
echo "Testing ArahPotensi root:"
curl -I http://103.23.198.101/ || echo "❌ ArahPotensi root failed"

echo ""
echo "✅ ARAHPOTENSI URL FIX COMPLETE!"
echo "===================================="
echo "🌐 ArahPotensi should now be accessible at: http://103.23.198.101/"
echo "🌐 All API calls should now go to: http://103.23.198.101/super-admin/api/*"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Fixed all hardcoded 127.0.0.1:8000 URLs"
echo "   ✅ Updated to production URLs (103.23.198.101/super-admin)"
echo "   ✅ Rebuilt the application"
echo "   ✅ Set proper permissions"
echo ""
echo "🎉 ArahPotensi should now work with production APIs!"
