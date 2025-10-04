#!/bin/bash
echo "🔧 Fixing Dashboard Redirect..."

cd /var/www/superadmin/superadmin-campusway

# Backup controller
cp app/Http/Controllers/SuperAdminController.php app/Http/Controllers/SuperAdminController.php.backup

# Fix the redirect in dashboard method
sed -i 's/return redirect(\x27\/login\x27)/return redirect(\x27\/super-admin\/login\x27)/g' app/Http/Controllers/SuperAdminController.php

# Verify the fix
echo "=== VERIFYING FIX ==="
grep -A 3 -B 3 "public function dashboard" app/Http/Controllers/SuperAdminController.php

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "✅ Dashboard redirect fixed!"
echo "🎯 Now testing redirect flow..."
