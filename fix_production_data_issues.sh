#!/bin/bash

echo "=== FIXING PRODUCTION DATA ISSUES ==="
echo ""

# 1. Clear all Laravel caches
echo "1. Clearing all Laravel caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
echo "   All caches cleared"
echo ""

# 2. Rebuild configuration
echo "2. Rebuilding configuration..."
php artisan config:cache
echo "   Configuration cached"
echo ""

# 3. Rebuild routes
echo "3. Rebuilding routes..."
php artisan route:cache
echo "   Routes cached"
echo ""

# 4. Check and fix storage permissions
echo "4. Fixing storage permissions..."
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
echo "   Storage permissions fixed"
echo ""

# 5. Check database migrations
echo "5. Checking database migrations..."
php artisan migrate:status
echo ""

# 6. Run any pending migrations
echo "6. Running pending migrations..."
php artisan migrate --force
echo ""

# 7. Check if database has data
echo "7. Checking database data..."
php -r "
require_once 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Database connection test...' . PHP_EOL;
try {
    \$connection = \Illuminate\Support\Facades\DB::connection();
    \$pdo = \$connection->getPdo();
    echo 'Database connected successfully' . PHP_EOL;
    
    \$tables = ['schools', 'students', 'major_recommendations', 'questions', 'subjects'];
    foreach (\$tables as \$table) {
        try {
            \$count = \Illuminate\Support\Facades\DB::table(\$table)->count();
            echo \$table . ': ' . \$count . ' records' . PHP_EOL;
        } catch (Exception \$e) {
            echo \$table . ': ERROR - ' . \$e->getMessage() . PHP_EOL;
        }
    }
} catch (Exception \$e) {
    echo 'Database connection failed: ' . \$e->getMessage() . PHP_EOL;
}
"
echo ""

# 8. Test API endpoints
echo "8. Testing API endpoints..."
echo "Testing /api/web/health..."
curl -s http://localhost/api/web/health | head -c 100
echo ""
echo "Testing /api/web/schools..."
curl -s http://localhost/api/web/schools | head -c 100
echo ""
echo "Testing /api/web/majors..."
curl -s http://localhost/api/web/majors | head -c 100
echo ""

# 9. Check web server configuration
echo "9. Checking web server configuration..."
if [ -f "/etc/apache2/sites-available/000-default.conf" ]; then
    echo "Apache configuration found"
    echo "Document root:"
    grep -i "DocumentRoot" /etc/apache2/sites-available/000-default.conf
elif [ -f "/etc/nginx/sites-available/default" ]; then
    echo "Nginx configuration found"
    echo "Root directory:"
    grep -i "root" /etc/nginx/sites-available/default
fi
echo ""

# 10. Restart web server
echo "10. Restarting web server..."
if systemctl is-active --quiet apache2; then
    echo "Restarting Apache..."
    systemctl restart apache2
    echo "Apache restarted"
elif systemctl is-active --quiet nginx; then
    echo "Restarting Nginx..."
    systemctl restart nginx
    echo "Nginx restarted"
fi
echo ""

# 11. Check PHP-FPM if using Nginx
if systemctl is-active --quiet php8.1-fpm; then
    echo "Restarting PHP-FPM..."
    systemctl restart php8.1-fpm
    echo "PHP-FPM restarted"
fi
echo ""

# 12. Final test
echo "12. Final test..."
echo "Testing main dashboard..."
curl -s http://localhost/dashboard | head -c 200
echo ""
echo "Testing schools page..."
curl -s http://localhost/schools | head -c 200
echo ""

echo "=== FIX COMPLETE ==="
echo ""
echo "If data is still not showing, check:"
echo "1. Database connection settings in .env"
echo "2. Database actually contains data"
echo "3. Web server error logs"
echo "4. Laravel logs in storage/logs/"
echo "5. File permissions on storage/ and bootstrap/cache/"
