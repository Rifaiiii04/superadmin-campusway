#!/bin/bash

echo "🔧 Fixing Middleware Error..."

# 1. Backup original Kernel.php
echo "💾 Backing up Kernel.php..."
cd /var/www/superadmin/superadmin-campusway
cp app/Http/Kernel.php app/Http/Kernel.php.backup

# 2. Comment out problematic middleware
echo "🔧 Disabling AddLinkHeadersForPreloadedAssets middleware..."
sed -i 's/\\Illuminate\\Http\\Middleware\\AddLinkHeadersForPreloadedAssets::class,/# \\Illuminate\\Http\\Middleware\\AddLinkHeadersForPreloadedAssets::class,/' app/Http/Kernel.php

# 3. Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 4. Test login page
echo "🧪 Testing login page..."
curl -I http://103.23.198.101/super-admin/login

echo "✅ Middleware fix complete!"
echo "🌐 Test: http://103.23.198.101/super-admin/login"
