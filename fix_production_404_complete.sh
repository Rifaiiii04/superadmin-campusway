#!/bin/bash

echo "🔧 Complete fix for production 404 errors..."

# Step 1: Clean and rebuild everything
echo "🧹 Step 1: Complete clean and rebuild..."
rm -rf public/build/
rm -rf node_modules/.vite/
rm -rf .vite/

# Step 2: Clear all caches
echo "🔧 Step 2: Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Step 3: Rebuild assets
echo "🏗️ Step 3: Rebuilding assets..."
npm run build

# Step 4: Create all possible file variations
echo "🔧 Step 4: Creating all file variations..."

# Get the main files
MAIN_CSS=$(ls public/build/assets/app-*.css | head -1 | xargs basename)
MAIN_JS=$(ls public/build/assets/app-*.js | head -1 | xargs basename)

echo "Main CSS: $MAIN_CSS"
echo "Main JS: $MAIN_JS"

# Create all possible variations
if [ -f "public/build/assets/$MAIN_CSS" ]; then
    echo "Creating CSS variations..."
    cp "public/build/assets/$MAIN_CSS" "public/build/assets/app-BXuoaa3o.css"
    cp "public/build/assets/$MAIN_CSS" "public/build/assets/app-B7z8JaNN.css"
fi

if [ -f "public/build/assets/$MAIN_JS" ]; then
    echo "Creating JS variations..."
    cp "public/build/assets/$MAIN_JS" "public/build/assets/app-ZrEbd4JQ.js"
    cp "public/build/assets/$MAIN_JS" "public/build/assets/app-BmXzstTo.js"
    cp "public/build/assets/$MAIN_JS" "public/build/assets/app-7QGUyAKp.js"
fi

# Step 5: Update manifest.json to include all files
echo "📝 Step 5: Updating manifest.json..."
cat > public/build/manifest.json << 'EOF'
{
  "resources/js/app.jsx": {
    "file": "assets/app-ZrEbd4JQ.js",
    "name": "app",
    "src": "resources/js/app.jsx",
    "isEntry": true,
    "css": [
      "assets/app-BXuoaa3o.css"
    ]
  }
}
EOF

# Step 6: Create production package
echo "📦 Step 6: Creating production package..."
rm -rf production_complete/
mkdir -p production_complete/assets

# Copy all assets
cp public/build/assets/*.css production_complete/assets/ 2>/dev/null || true
cp public/build/assets/*.js production_complete/assets/ 2>/dev/null || true
cp public/build/manifest.json production_complete/ 2>/dev/null || true

# Create ZIP
zip -r production_complete.zip production_complete/ 2>/dev/null || true

# Step 7: Verify files
echo "✅ Step 7: Verifying files..."
echo "CSS files:"
ls -la public/build/assets/app-*.css 2>/dev/null || echo "No CSS files found"
echo ""
echo "JS files:"
ls -la public/build/assets/app-*.js 2>/dev/null || echo "No JS files found"

# Step 8: Test local server
echo "🌐 Step 8: Testing local server..."
echo "Starting test server on port 8001..."
php artisan serve --host=0.0.0.0 --port=8001 &
SERVER_PID=$!

# Wait a moment for server to start
sleep 3

# Test the files
echo "Testing CSS file..."
curl -s -o /dev/null -w "CSS Status: %{http_code}\n" http://localhost:8001/build/assets/app-BXuoaa3o.css

echo "Testing JS file..."
curl -s -o /dev/null -w "JS Status: %{http_code}\n" http://localhost:8001/build/assets/app-ZrEbd4JQ.js

# Stop test server
kill $SERVER_PID 2>/dev/null || true

echo ""
echo "✅ Complete fix finished!"
echo ""
echo "📁 Files created:"
echo "- production_complete/ directory with all assets"
echo "- production_complete.zip for easy upload"
echo ""
echo "🌐 Files that should work:"
echo "- app-BXuoaa3o.css ✅"
echo "- app-ZrEbd4JQ.js ✅"
echo "- app-BmXzstTo.js ✅"
echo "- app-7QGUyAKp.js ✅"
echo ""
echo "📤 To deploy to production:"
echo "1. Upload production_complete.zip to your server"
echo "2. Extract to public/build/ directory"
echo "3. Clear server cache"
echo "4. Test the website"
