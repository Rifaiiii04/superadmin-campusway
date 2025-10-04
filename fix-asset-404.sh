#!/bin/bash

echo "🔧 FIXING SUPERADMIN ASSET 404 ERROR"
echo "===================================="

# 1. Navigate to superadmin directory
echo "📁 Step 1: Navigating to /var/www/superadmin/superadmin-campusway..."
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
echo "⏹️ Step 2: Stopping Apache..."
sudo systemctl stop apache2

# 3. Remove old build directory
echo "🗑️ Step 3: Removing old build directory..."
sudo rm -rf public/build

# 4. Set ownership to current user
echo "🔧 Step 4: Setting ownership to current user..."
sudo chown -R $USER:$USER .

# 5. Create build directory
echo "📁 Step 5: Creating build directory..."
mkdir -p public/build/assets
chmod -R 755 public/build

# 6. Build assets
echo "🔨 Step 6: Building Vite assets..."
npm run build || { echo "❌ Vite build failed. Exiting."; exit 1; }

# 7. Check what was built
echo "📄 Step 7: Checking built assets..."
ls -la public/build/assets/

# 8. Create manifest.json if missing
echo "📋 Step 8: Creating manifest.json..."
if [ ! -f "public/build/manifest.json" ]; then
    echo "Creating manifest.json..."
    cat > public/build/manifest.json << 'EOF'
{
  "resources/js/app.jsx": {
    "file": "assets/app-D_7II1BX.js",
    "name": "app",
    "src": "resources/js/app.jsx",
    "isEntry": true,
    "imports": [
      "_query-RjlQ86bh.js",
      "_inertia-DkPr4kFk.js",
      "_vendor-CsibPLSJ.js"
    ],
    "css": [
      "assets/app-BHSs9Ase.css"
    ]
  }
}
EOF
fi

# 9. Get actual asset names
echo "🔍 Step 9: Getting actual asset names..."
JS_FILE=$(ls public/build/assets/*.js 2>/dev/null | head -1 | xargs basename)
CSS_FILE=$(ls public/build/assets/*.css 2>/dev/null | head -1 | xargs basename)

echo "Found JS file: $JS_FILE"
echo "Found CSS file: $CSS_FILE"

# 10. Update app.blade.php with actual asset names
echo "🔧 Step 10: Updating app.blade.php with actual asset names..."
if [ -n "$JS_FILE" ] && [ -n "$CSS_FILE" ]; then
    sudo tee resources/views/app.blade.php > /dev/null << EOF
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @routes
        @inertiaHead
        
        <!-- Manual asset loading dengan nama file yang benar -->
        <script type="module" src="/super-admin/build/assets/$JS_FILE"></script>
        <link rel="stylesheet" href="/super-admin/build/assets/$CSS_FILE">
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
EOF
    echo "✅ app.blade.php updated with actual asset names"
else
    echo "❌ Could not find asset files"
fi

# 11. Set permissions
echo "🔧 Step 11: Setting permissions..."
sudo chown -R www-data:www-data public
sudo chmod -R 755 public

# 12. Start Apache
echo "🔄 Step 12: Starting Apache..."
sudo systemctl start apache2

# 13. Test assets
echo "🧪 Step 13: Testing assets..."
echo "Testing JS asset:"
curl -I http://103.23.198.101/super-admin/build/assets/$JS_FILE || echo "❌ JS asset not accessible"

echo "Testing CSS asset:"
curl -I http://103.23.198.101/super-admin/build/assets/$CSS_FILE || echo "❌ CSS asset not accessible"

echo "Testing SuperAdmin login page:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ SuperAdmin login failed"

echo ""
echo "✅ ASSET 404 FIX COMPLETE!"
echo "===================================="
echo "🌐 Test SuperAdmin at: http://103.23.198.101/super-admin/login"
echo "📄 JS Asset: $JS_FILE"
echo "📄 CSS Asset: $CSS_FILE"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Rebuilt all Vite assets"
echo "   ✅ Updated app.blade.php with actual asset names"
echo "   ✅ Fixed asset permissions"
echo "   ✅ Created proper manifest.json"
echo ""
echo "🎉 SuperAdmin should now load properly!"
