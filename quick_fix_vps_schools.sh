#!/bin/bash

echo "🔧 Quick Fix for Schools Page on VPS Production..."

# Navigate to project directory
cd /path/to/superadmin-backend

# 1. Fix database structure
echo "1. Fixing database structure..."
php -r "
require_once 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Copy password_hash to password where password is NULL
    \$updated = DB::update('UPDATE schools SET password = password_hash WHERE password IS NULL');
    echo 'Updated ' . \$updated . ' records with password data\n';
    
    // Make password NOT NULL
    DB::statement('ALTER TABLE schools MODIFY COLUMN password VARCHAR(255) NOT NULL');
    echo 'Password field is now NOT NULL\n';
    
    // Drop password_hash column
    DB::statement('ALTER TABLE schools DROP COLUMN password_hash');
    echo 'password_hash column dropped\n';
    
    echo 'Database structure fixed successfully!\n';
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . '\n';
}
"

# 2. Clear Laravel cache
echo "2. Clearing Laravel cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 3. Rebuild frontend assets
echo "3. Rebuilding frontend assets..."
npm run build

# 4. Restart services
echo "4. Restarting services..."
systemctl restart apache2
systemctl restart php8.1-fpm

echo "✅ Quick fix completed!"
echo "The schools page should now work properly."
