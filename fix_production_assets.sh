#!/bin/bash

echo "🔧 Fixing production assets 404 error..."

# Rebuild assets to ensure we have the latest
echo "📦 Rebuilding assets..."
npm run build

# Check what files we have
echo ""
echo "📁 Current assets:"
echo "CSS files:"
ls -la public/build/assets/app-*.css
echo ""
echo "JS files:"
ls -la public/build/assets/app-*.js

# Create a simple upload script
echo ""
echo "📝 Creating upload instructions..."

cat > upload_instructions.txt << EOF
# Upload Instructions for Production Server

## Files to upload to: http://103.23.198.101:8080

### 1. Upload these files to /public/build/assets/:
$(ls public/build/assets/app-*.css public/build/assets/app-*.js | sed 's/^/- /')

### 2. Upload updated source files:
- resources/js/Pages/SuperAdmin/MajorRecommendations.jsx
- app/Http/Controllers/MajorRecommendationController.php

### 3. Run these commands on production server:
cd /path/to/your/laravel/app
php artisan config:clear
php artisan cache:clear
php artisan view:clear

### 4. Check web server configuration:
Make sure your web server (Apache/Nginx) is configured to serve files from the public/build directory.

### 5. Verify files exist:
Check that these files exist on production:
- /public/build/assets/app-BXuoaa3o.css
- /public/build/assets/app-ZrEbd4JQ.js (or latest app-*.js)

### 6. Browser cache:
Tell users to hard refresh (Ctrl+F5) or clear browser cache.
EOF

echo "✅ Instructions created in upload_instructions.txt"
echo ""
echo "📋 Manual steps to fix production:"
echo "1. Upload the assets files shown above to your production server"
echo "2. Upload the updated source files"
echo "3. Run the Laravel cache clear commands on production"
echo "4. Hard refresh browser (Ctrl+F5)"
echo ""
echo "🔍 Current asset files that need to be uploaded:"
ls -la public/build/assets/app-*.css public/build/assets/app-*.js
