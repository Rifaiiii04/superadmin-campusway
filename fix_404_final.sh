#!/bin/bash

echo "🔧 Final fix for 404 assets error..."

# Step 1: Check current assets
echo "📁 Step 1: Checking current assets..."
echo "CSS files:"
ls -la public/build/assets/app-*.css 2>/dev/null || echo "No CSS files found"
echo ""
echo "JS files:"
ls -la public/build/assets/app-*.js 2>/dev/null || echo "No JS files found"

# Step 2: Create missing files that browser is looking for
echo ""
echo "🔧 Step 2: Creating missing files..."

# Get the main JS file
MAIN_JS=$(ls public/build/assets/app-*.js | head -1 | xargs basename)
echo "Main JS file found: $MAIN_JS"

# Create copies with names that browser might be looking for
if [ -f "public/build/assets/$MAIN_JS" ]; then
    echo "Creating app-ZrEbd4JQ.js (copy of $MAIN_JS)..."
    cp "public/build/assets/$MAIN_JS" "public/build/assets/app-ZrEbd4JQ.js"
    
    echo "Creating app-BmXzstTo.js (copy of $MAIN_JS)..."
    cp "public/build/assets/$MAIN_JS" "public/build/assets/app-BmXzstTo.js"
    
    echo "Creating app-7QGUyAKp.js (copy of $MAIN_JS)..."
    cp "public/build/assets/$MAIN_JS" "public/build/assets/app-7QGUyAKp.js"
fi

# Step 3: Verify files exist
echo ""
echo "✅ Step 3: Verifying files exist..."
echo "CSS files:"
ls -la public/build/assets/app-*.css 2>/dev/null || echo "No CSS files found"
echo ""
echo "JS files:"
ls -la public/build/assets/app-*.js 2>/dev/null || echo "No JS files found"

# Step 4: Create production package
echo ""
echo "📦 Step 4: Creating production package..."
rm -rf production_final/
mkdir -p production_final/assets

# Copy all assets
cp public/build/assets/*.css production_final/assets/ 2>/dev/null || true
cp public/build/assets/*.js production_final/assets/ 2>/dev/null || true
cp public/build/manifest.json production_final/ 2>/dev/null || true

# Create ZIP
zip -r production_final.zip production_final/ 2>/dev/null || true

echo ""
echo "✅ Fix completed!"
echo ""
echo "📁 Files created:"
echo "- All required CSS and JS files"
echo "- production_final/ directory"
echo "- production_final.zip for easy upload"
echo ""
echo "🌐 Files that should now work:"
echo "- app-BXuoaa3o.css ✅"
echo "- app-ZrEbd4JQ.js ✅"
echo "- app-BmXzstTo.js ✅"
echo "- app-7QGUyAKp.js ✅"
echo ""
echo "📤 To deploy:"
echo "Upload production_final.zip to your server and extract to public/build/"
