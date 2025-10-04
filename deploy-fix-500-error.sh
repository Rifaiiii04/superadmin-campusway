#!/bin/bash

echo "🚀 Deploying SuperAdmin 500 Error Fix..."

# 1. Navigate to the superadmin directory
echo "📁 Navigating to /var/www/superadmin/superadmin-campusway..."
cd /var/www/superadmin/superadmin-campusway || { echo "❌ Failed to navigate to superadmin-campusway. Exiting."; exit 1; }

# 2. Pull latest changes
echo "📥 Pulling latest changes from Git..."
git pull origin main || { echo "❌ Git pull failed. Exiting."; exit 1; }

# 3. Set permissions
echo "🔧 Setting permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 4. Install dependencies
echo "📦 Installing composer dependencies..."
composer install --no-dev --optimize-autoloader || { echo "❌ Composer install failed. Exiting."; exit 1; }

# 5. Clear all caches
echo "🧹 Clearing all Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize:clear

# 6. Rebuild assets
echo "🔨 Rebuilding Vite assets..."
npm run build || { echo "❌ Vite build failed. Exiting."; exit 1; }

# 7. Set permissions for built assets
echo "🔧 Setting permissions for built assets..."
sudo chown -R www-data:www-data public/build
sudo chmod -R 755 public/build

# 8. Run migrations (if any)
echo "🗄️ Running database migrations..."
php artisan migrate --force || { echo "⚠️ Migrations failed, but continuing..." }

# 9. Create/Update admin user
echo "👤 Creating/Updating admin user..."
php artisan tinker --execute="
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
\$admin = Admin::where('username', 'admin')->first();
if (!\$admin) {
    \$admin = Admin::create(['name' => 'Super Admin', 'username' => 'admin', 'password' => Hash::make('password123')]);
    echo 'Admin created';
} else {
    \$admin->update(['password' => Hash::make('password123')]);
    echo 'Admin updated';
}
" || { echo "⚠️ Admin user creation/update failed, but continuing..." }

# 10. Restart Apache
echo "🔄 Restarting Apache web server..."
sudo systemctl restart apache2 || { echo "⚠️ Apache restart failed. Please check manually." }

# 11. Test the application
echo "🧪 Testing the application..."
echo "Testing route /test..."
curl -s http://103.23.198.101/super-admin/test || echo "❌ Test route failed"

echo "Testing login page..."
curl -I http://103.23.198.101/super-admin/login || echo "❌ Login page failed"

echo "✅ Deployment complete!"
echo "🌐 Test the application at: http://103.23.198.101/super-admin/login"
echo "👤 Login credentials:"
echo "   Username: admin"
echo "   Password: password123"
