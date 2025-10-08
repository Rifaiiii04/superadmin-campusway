#!/bin/bash

echo "🚀 Quick fix for production 404 errors..."

# Rebuild assets
echo "📦 Rebuilding assets..."
npm run build

# Create missing file that production is looking for
echo "🔧 Creating missing app-BmXzstTo.js file..."
cp public/build/assets/app-ZrEbd4JQ.js public/build/assets/app-BmXzstTo.js

# Verify files exist
echo ""
echo "📁 Verifying files exist:"
echo "CSS files:"
ls -la public/build/assets/app-*.css
echo ""
echo "JS files:"
ls -la public/build/assets/app-*.js

# Create a simple upload package
echo ""
echo "📦 Creating upload package..."
mkdir -p production_upload/assets
cp public/build/assets/app-*.css production_upload/assets/
cp public/build/assets/app-*.js production_upload/assets/
cp public/build/manifest.json production_upload/

echo "✅ Upload package created in production_upload/ directory"
echo ""
echo "📋 Files to upload to production server:"
echo "1. Upload entire production_upload/ directory to your server's public/build/ path"
echo "2. Or upload these specific files:"
ls -la production_upload/assets/

echo ""
echo "🌐 Production server: http://103.23.198.101:8080"
echo "📁 Upload to: /public/build/assets/ (or your Laravel public/build/assets/ path)"
echo ""
echo "🔄 After uploading, run on production server:"
echo "php artisan config:clear"
echo "php artisan cache:clear"
echo "php artisan view:clear"
echo ""
echo "🔄 Then hard refresh browser (Ctrl+F5)"
