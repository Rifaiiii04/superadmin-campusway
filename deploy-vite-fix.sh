#!/bin/bash

echo "🔧 DEPLOY VITE FIX - UPDATE CONFIG + APP.BLADE.PHP"
echo "================================================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Fix git ownership
echo "🔧 Step 1: Fixing git ownership..."
sudo git config --global --add safe.directory /var/www/superadmin/superadmin-campusway

# 3. Pull latest changes
echo "🔧 Step 2: Pulling latest changes..."
git pull origin main

# 4. Update Vite config
echo "🔧 Step 3: Updating Vite config..."
cat > vite.config.js << 'EOF'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        react(),
    ],
    server: {
        host: '0.0.0.0',
    },
    build: {
        outDir: 'public/build',
        assetsDir: 'assets',
    },
});
EOF

# 5. Update app.blade.php
echo "🔧 Step 4: Updating app.blade.php..."
cat > resources/views/app.blade.php << 'EOF'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    
    <!-- Manual CSS -->
    <link href="/super-admin/build/assets/<?php echo \File::glob(public_path('build/assets/app-*.css'))[0] ?? 'app.css'; ?>" rel="stylesheet">
    
    <title>SuperAdmin</title>
</head>
<body>
    @inertia
    
    <!-- Manual JS -->
    <script src="/super-admin/build/assets/<?php echo \File::glob(public_path('build/assets/app-*.js'))[0] ?? 'app.js'; ?>"></script>
</body>
</html>
EOF

# 6. Build assets
echo "🔧 Step 5: Building assets..."
npm install
npm run build

# 7. Fix permissions
echo "🔧 Step 6: Fixing permissions..."
sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway
sudo chmod -R 755 /var/www/superadmin/superadmin-campusway/public
sudo chmod -R 775 /var/www/superadmin/superadmin-campusway/storage
sudo chmod -R 775 /var/www/superadmin/superadmin-campusway/bootstrap/cache

# 8. Clear Laravel caches
echo "🔧 Step 7: Clearing Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 9. Restart Apache
echo "🔄 Step 8: Restarting Apache..."
sudo systemctl restart apache2

# 10. Test everything
echo "🧪 Step 9: Testing everything..."
echo "Testing SuperAdmin test:"
curl -s http://103.23.198.101/super-admin/test || echo "❌ Test failed"
echo ""

echo "Testing SuperAdmin login:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ Login failed"
echo ""

echo "Testing build directory:"
curl -I http://103.23.198.101/super-admin/build/ || echo "❌ Build directory failed"
echo ""

echo "Testing Next.js:"
curl -I http://103.23.198.101/ || echo "❌ Next.js failed"
echo ""

echo "✅ VITE FIX DEPLOY COMPLETE!"
echo "==========================="
echo "🌐 SuperAdmin: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/"
echo ""
echo "📋 What was updated:"
echo "   ✅ Updated Vite config (simplified)"
echo "   ✅ Updated app.blade.php (manual assets)"
echo "   ✅ Built assets with new config"
echo "   ✅ Fixed permissions"
echo "   ✅ Cleared Laravel caches"
echo "   ✅ Restarted Apache"
echo ""
echo "🎉 SuperAdmin should now work with manual assets!"
