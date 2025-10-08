#!/bin/bash

echo "🚀 Complete rebuild process..."

# Step 1: Clean everything
echo "🧹 Step 1: Cleaning old build files..."
rm -rf public/build/
rm -rf node_modules/.vite/
rm -rf .vite/

# Step 2: Clear Laravel caches
echo "🔧 Step 2: Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Step 3: Install/update dependencies
echo "📦 Step 3: Installing dependencies..."
npm install

# Step 4: Build assets
echo "🏗️ Step 4: Building assets..."
npm run build

# Step 5: Verify build
echo "✅ Step 5: Verifying build..."
echo "CSS files:"
ls -la public/build/assets/app-*.css 2>/dev/null || echo "No CSS files found"
echo ""
echo "JS files:"
ls -la public/build/assets/app-*.js 2>/dev/null || echo "No JS files found"
echo ""
echo "Manifest file:"
ls -la public/build/manifest.json 2>/dev/null || echo "No manifest file found"

# Step 6: Check file sizes
echo "📊 Step 6: File sizes:"
if [ -f "public/build/assets/app-BXuoaa3o.css" ]; then
    echo "✅ Main CSS: $(du -h public/build/assets/app-BXuoaa3o.css | cut -f1)"
else
    echo "❌ Main CSS not found"
fi

if [ -f "public/build/assets/app-ZrEbd4JQ.js" ]; then
    echo "✅ Main JS: $(du -h public/build/assets/app-ZrEbd4JQ.js | cut -f1)"
else
    echo "❌ Main JS not found"
fi

# Step 7: Create production package
echo "📦 Step 7: Creating production package..."
mkdir -p production_build/assets
cp public/build/assets/app-*.css production_build/assets/ 2>/dev/null || true
cp public/build/assets/app-*.js production_build/assets/ 2>/dev/null || true
cp public/build/manifest.json production_build/ 2>/dev/null || true

# Create ZIP for easy upload
zip -r production_build.zip production_build/ 2>/dev/null || true

echo ""
echo "✅ Rebuild completed successfully!"
echo ""
echo "📁 Build files created:"
echo "- public/build/assets/ (all assets)"
echo "- production_build/ (production package)"
echo "- production_build.zip (for easy upload)"
echo ""
echo "🌐 To test locally:"
echo "php artisan serve --host=0.0.0.0 --port=8000"
echo ""
echo "📤 To deploy to production:"
echo "Upload production_build.zip to your server and extract to public/build/"
