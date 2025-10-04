#!/bin/bash

echo "🔍 CHECKING ASSETS EXISTENCE"
echo "============================"

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Check build directory
echo "📋 Step 1: Checking build directory..."
echo "=== BUILD DIRECTORY ==="
ls -la public/build/
echo ""

# 3. Check assets directory
echo "📋 Step 2: Checking assets directory..."
echo "=== ASSETS DIRECTORY ==="
ls -la public/build/assets/ | head -20
echo ""

# 4. Check manifest.json
echo "📋 Step 3: Checking manifest.json..."
echo "=== MANIFEST.JSON ==="
if [ -f "public/build/manifest.json" ]; then
    echo "✅ manifest.json exists"
    echo "=== APP.JSX ENTRY ==="
    grep -A 10 -B 2 "resources/js/app.jsx" public/build/manifest.json
    echo ""
    echo "=== JS ASSET ==="
    JS_ASSET=$(grep -oP '"resources/js/app.jsx":\s*\{\s*"file":\s*"\K[^"]+' public/build/manifest.json)
    echo "JS Asset: $JS_ASSET"
    echo ""
    echo "=== CSS ASSET ==="
    CSS_ASSET=$(grep -oP '"resources/js/app.jsx":\s*\{\s*[^}]*"css":\s*\[\s*"\K[^"]+' public/build/manifest.json)
    echo "CSS Asset: $CSS_ASSET"
    echo ""
    echo "=== CHECKING ASSETS EXIST ==="
    if [ -f "public/build/$JS_ASSET" ]; then
        echo "✅ JS Asset exists: $JS_ASSET"
    else
        echo "❌ JS Asset missing: $JS_ASSET"
    fi
    if [ -f "public/build/$CSS_ASSET" ]; then
        echo "✅ CSS Asset exists: $CSS_ASSET"
    else
        echo "❌ CSS Asset missing: $CSS_ASSET"
    fi
else
    echo "❌ manifest.json not found"
fi
echo ""

# 5. Check if assets are accessible via web
echo "📋 Step 4: Testing asset URLs..."
echo "=== ASSET URLS ==="
if [ -n "$JS_ASSET" ]; then
    curl -I http://103.23.198.101/super-admin/build/$JS_ASSET 2>/dev/null || echo "❌ JS Asset URL failed"
fi
if [ -n "$CSS_ASSET" ]; then
    curl -I http://103.23.198.101/super-admin/build/$CSS_ASSET 2>/dev/null || echo "❌ CSS Asset URL failed"
fi
echo ""

# 6. Check app.blade.php
echo "📋 Step 5: Checking app.blade.php..."
echo "=== APP.BLADE.PHP ==="
grep -A 5 -B 5 "build/assets" resources/views/app.blade.php
echo ""

echo "✅ ASSETS CHECK COMPLETE!"
echo "Check the output above for issues."
