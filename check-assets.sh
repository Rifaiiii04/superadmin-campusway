#!/bin/bash

echo "🔍 CHECKING SUPERADMIN ASSETS"
echo "============================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Check if build directory exists
echo "📁 Checking build directory..."
ls -la public/build/

# 3. Check if assets exist
echo "📄 Checking specific assets..."
ls -la public/build/assets/ | grep app-D_7II1BX.js || echo "❌ app-D_7II1BX.js not found"
ls -la public/build/assets/ | grep app-BHSs9Ase.css || echo "❌ app-BHSs9Ase.css not found"

# 4. Check manifest.json
echo "📋 Checking manifest.json..."
cat public/build/manifest.json || echo "❌ manifest.json not found"

# 5. Check permissions
echo "🔧 Checking permissions..."
ls -la public/build/
ls -la public/build/assets/

# 6. Test asset URLs
echo "🌐 Testing asset URLs..."
curl -I http://103.23.198.101/super-admin/build/assets/app-D_7II1BX.js || echo "❌ JS asset not accessible"
curl -I http://103.23.198.101/super-admin/build/assets/app-BHSs9Ase.css || echo "❌ CSS asset not accessible"

echo "✅ Asset check complete!"
