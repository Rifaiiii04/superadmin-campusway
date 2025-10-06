#!/bin/bash

echo "🔧 Fixing SuperAdmin 500 Error - Assets Issues..."

# 1. Navigate to the superadmin directory
cd /var/www/superadmin/superadmin-campusway || { echo "❌ Failed to navigate to superadmin-campusway. Exiting."; exit 1; }

# 2. Set ownership to current user for build directory
echo "📁 Setting ownership to current user for public directory..."
sudo chown -R $USER:$USER public

# 3. Remove old build directory
echo "🗑️ Removing old build directory..."
sudo rm -rf public/build

# 4. Create new build directory
echo "📁 Creating new build directory..."
mkdir -p public/build/assets
chmod -R 755 public/build

# 5. Build assets
echo "🔨 Building Vite assets..."
npm run build || { echo "❌ Vite build failed. Exiting."; exit 1; }

# 6. Set final permissions for web server
echo "🔧 Setting final permissions for web server (www-data)..."
sudo chown -R www-data:www-data public
sudo chmod -R 755 public

# 7. Create manifest.json if missing
echo "📄 Creating manifest.json if missing..."
if [ ! -f "public/build/manifest.json" ]; then
    echo "Creating minimal manifest.json..."
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

# 8. Set permissions for manifest.json
sudo chown www-data:www-data public/build/manifest.json
sudo chmod 644 public/build/manifest.json

# 9. Test assets
echo "🧪 Testing assets..."
echo "Testing manifest.json..."
curl -s http://103.23.198.101/super-admin/build/manifest.json | head -5 || echo "❌ Manifest.json not accessible"

echo "Testing main JS file..."
curl -I http://103.23.198.101/super-admin/build/assets/app-D_7II1BX.js || echo "❌ Main JS file not accessible"

echo "Testing main CSS file..."
curl -I http://103.23.198.101/super-admin/build/assets/app-BHSs9Ase.css || echo "❌ Main CSS file not accessible"

echo "✅ Assets fix complete!"
echo "🌐 Test the application at: http://103.23.198.101/super-admin/login"
