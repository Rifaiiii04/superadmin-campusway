#!/bin/bash

echo "🔍 DEBUGGING 500 INTERNAL SERVER ERROR"
echo "====================================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Check Laravel logs
echo "📋 Step 1: Checking Laravel logs..."
echo "=== LARAVEL LOG ==="
tail -50 storage/logs/laravel.log || echo "❌ No Laravel log found"

# 3. Check Apache error logs
echo ""
echo "📋 Step 2: Checking Apache error logs..."
echo "=== APACHE ERROR LOG ==="
sudo tail -50 /var/log/apache2/error.log || echo "❌ No Apache error log found"

# 4. Check PHP error logs
echo ""
echo "📋 Step 3: Checking PHP error logs..."
echo "=== PHP ERROR LOG ==="
sudo tail -50 /var/log/php8.3-fpm.log || echo "❌ No PHP error log found"

# 5. Check file permissions
echo ""
echo "📋 Step 4: Checking file permissions..."
echo "=== STORAGE PERMISSIONS ==="
ls -la storage/
echo "=== BOOTSTRAP CACHE PERMISSIONS ==="
ls -la bootstrap/cache/

# 6. Check if .env exists
echo ""
echo "📋 Step 5: Checking .env file..."
if [ -f ".env" ]; then
    echo "✅ .env file exists"
    echo "APP_ENV: $(grep APP_ENV .env | cut -d'=' -f2)"
    echo "APP_DEBUG: $(grep APP_DEBUG .env | cut -d'=' -f2)"
else
    echo "❌ .env file not found"
fi

# 7. Test PHP syntax
echo ""
echo "📋 Step 6: Testing PHP syntax..."
php -l app/Http/Kernel.php || echo "❌ Kernel.php syntax error"
php -l routes/web.php || echo "❌ web.php syntax error"

# 8. Test artisan commands
echo ""
echo "📋 Step 7: Testing artisan commands..."
php artisan --version || echo "❌ Artisan not working"
php artisan config:show app.name || echo "❌ Config not working"

# 9. Check disk space
echo ""
echo "📋 Step 8: Checking disk space..."
df -h

echo ""
echo "✅ DEBUG COMPLETE!"
echo "Check the logs above for error details."
