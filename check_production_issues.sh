#!/bin/bash

echo "=== PRODUCTION VPS DIAGNOSIS SCRIPT ==="
echo ""

# 1. Check Laravel logs
echo "1. Checking Laravel logs..."
if [ -f "storage/logs/laravel.log" ]; then
    echo "   Laravel log exists"
    echo "   Last 20 lines of error log:"
    tail -20 storage/logs/laravel.log | grep -i error || echo "   No errors found in last 20 lines"
else
    echo "   Laravel log not found"
fi
echo ""

# 2. Check web server logs
echo "2. Checking web server logs..."
if [ -f "/var/log/apache2/error.log" ]; then
    echo "   Apache error log exists"
    echo "   Recent errors:"
    tail -10 /var/log/apache2/error.log | grep -i error || echo "   No recent errors"
elif [ -f "/var/log/nginx/error.log" ]; then
    echo "   Nginx error log exists"
    echo "   Recent errors:"
    tail -10 /var/log/nginx/error.log | grep -i error || echo "   No recent errors"
else
    echo "   Web server log not found"
fi
echo ""

# 3. Check PHP logs
echo "3. Checking PHP logs..."
if [ -f "/var/log/php_errors.log" ]; then
    echo "   PHP error log exists"
    echo "   Recent errors:"
    tail -10 /var/log/php_errors.log | grep -i error || echo "   No recent errors"
else
    echo "   PHP error log not found"
fi
echo ""

# 4. Check Laravel cache
echo "4. Checking Laravel cache..."
if [ -d "bootstrap/cache" ]; then
    echo "   Cache directory exists"
    ls -la bootstrap/cache/
else
    echo "   Cache directory not found"
fi
echo ""

# 5. Check storage permissions
echo "5. Checking storage permissions..."
if [ -d "storage" ]; then
    echo "   Storage directory permissions:"
    ls -la storage/
    echo "   Storage subdirectories:"
    find storage -type d -exec ls -ld {} \;
else
    echo "   Storage directory not found"
fi
echo ""

# 6. Check .env file
echo "6. Checking environment file..."
if [ -f ".env" ]; then
    echo "   .env file exists"
    echo "   Database configuration:"
    grep -E "^DB_" .env || echo "   No DB configuration found"
else
    echo "   .env file not found"
fi
echo ""

# 7. Check database connection
echo "7. Testing database connection..."
php -r "
try {
    require_once 'vendor/autoload.php';
    \$app = require_once 'bootstrap/app.php';
    \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    \$connection = \Illuminate\Support\Facades\DB::connection();
    \$pdo = \$connection->getPdo();
    echo '   Database connection: SUCCESS\n';
    echo '   Database name: ' . \$connection->getDatabaseName() . '\n';
    
    // Test a simple query
    \$result = \Illuminate\Support\Facades\DB::select('SELECT COUNT(*) as count FROM schools');
    echo '   Schools count: ' . \$result[0]->count . '\n';
    
} catch (Exception \$e) {
    echo '   Database connection: FAILED\n';
    echo '   Error: ' . \$e->getMessage() . '\n';
}
"
echo ""

# 8. Check if data exists in database
echo "8. Checking data existence..."
php -r "
try {
    require_once 'vendor/autoload.php';
    \$app = require_once 'bootstrap/app.php';
    \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    \$tables = ['schools', 'students', 'major_recommendations', 'questions', 'subjects'];
    
    foreach (\$tables as \$table) {
        try {
            \$count = \Illuminate\Support\Facades\DB::table(\$table)->count();
            echo '   ' . \$table . ': ' . \$count . ' records\n';
        } catch (Exception \$e) {
            echo '   ' . \$table . ': ERROR - ' . \$e->getMessage() . '\n';
        }
    }
    
} catch (Exception \$e) {
    echo '   Error checking data: ' . \$e->getMessage() . '\n';
}
"
echo ""

# 9. Clear Laravel cache
echo "9. Clearing Laravel cache..."
php artisan cache:clear 2>/dev/null && echo "   Cache cleared successfully" || echo "   Failed to clear cache"
php artisan config:clear 2>/dev/null && echo "   Config cache cleared successfully" || echo "   Failed to clear config cache"
php artisan route:clear 2>/dev/null && echo "   Route cache cleared successfully" || echo "   Failed to clear route cache"
php artisan view:clear 2>/dev/null && echo "   View cache cleared successfully" || echo "   Failed to clear view cache"
echo ""

# 10. Check if migrations are up to date
echo "10. Checking migrations..."
php artisan migrate:status 2>/dev/null || echo "   Failed to check migration status"
echo ""

echo "=== DIAGNOSIS COMPLETE ==="
