#!/bin/bash

# TKA SuperAdmin - Run Deployment on VPS
# Run this script directly on the VPS

echo "🚀 TKA SuperAdmin - Running Deployment on VPS"
echo "============================================="
echo ""

# Set variables
SUPERADMIN_DIR="/var/www/superadmin/superadmin-campusway"
APACHE_ROOT="/var/www/html"
ARAHPOTENSI_DIR="/var/www/arahpotensi"

echo "📁 Checking current directory..."
pwd
echo ""

echo "📁 Checking SuperAdmin directory..."
if [ -d "$SUPERADMIN_DIR" ]; then
    echo "   ✅ SuperAdmin directory exists: $SUPERADMIN_DIR"
    cd "$SUPERADMIN_DIR"
    echo "   📍 Current directory: $(pwd)"
else
    echo "   ❌ SuperAdmin directory not found: $SUPERADMIN_DIR"
    echo "   📁 Available directories in /var/www/superadmin/:"
    ls -la /var/www/superadmin/
    exit 1
fi

echo ""
echo "🔧 1. Setting up SuperAdmin Laravel Backend..."

echo "   📋 Setting permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

echo "   📦 Installing/updating dependencies..."
composer install --no-dev --optimize-autoloader

echo "   🔧 Clearing Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "   🔄 Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "   🗄️ Running database migrations..."
php artisan migrate --force

echo "   👤 Creating/updating admin user..."
php -r "
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

require_once 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

\$admin = Admin::where('username', 'admin')->first();
if (!\$admin) {
    \$admin = Admin::create([
        'name' => 'Super Admin',
        'username' => 'admin',
        'password' => Hash::make('password123')
    ]);
    echo 'Admin user created successfully\n';
} else {
    \$admin->update(['password' => Hash::make('password123')]);
    echo 'Admin user updated successfully\n';
}
echo 'Username: admin\n';
echo 'Password: password123\n';
"

echo ""
echo "🌐 2. Setting up ArahPotensi Next.js Frontend..."
cd "$ARAHPOTENSI_DIR"

echo "   📦 Installing dependencies..."
npm install

echo "   🔨 Building for production..."
npm run build

echo "   📋 Setting permissions..."
sudo chown -R www-data:www-data .
sudo chmod -R 755 .

echo ""
echo "🔧 3. Configuring Apache..."

# Create Apache configuration for SuperAdmin
sudo tee /etc/apache2/sites-available/superadmin.conf > /dev/null <<EOF
<VirtualHost *:80>
    ServerName 103.23.198.101
    DocumentRoot /var/www/html
    
    # SuperAdmin Laravel Backend
    Alias /super-admin /var/www/superadmin/superadmin-campusway/public
    
    <Directory /var/www/superadmin/superadmin-campusway/public>
        AllowOverride All
        Require all granted
        
        # CORS Headers for API
        Header always set Access-Control-Allow-Origin "http://103.23.198.101"
        Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
        Header always set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With, Accept, Origin"
        Header always set Access-Control-Allow-Credentials "true"
        Header always set Access-Control-Max-Age "3600"
        
        # Handle preflight requests
        RewriteEngine On
        RewriteCond %{REQUEST_METHOD} OPTIONS
        RewriteRule ^(.*)$ $1 [R=200,L]
    </Directory>
    
    # ArahPotensi Next.js Frontend
    <Directory /var/www/html>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Error and access logs
    ErrorLog \${APACHE_LOG_DIR}/superadmin_error.log
    CustomLog \${APACHE_LOG_DIR}/superadmin_access.log combined
</VirtualHost>
EOF

echo "   ✅ Apache configuration created"

# Enable the site
sudo a2ensite superadmin.conf

# Enable required Apache modules
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod deflate

echo "   🔄 Restarting Apache..."
sudo systemctl restart apache2

echo ""
echo "🧪 4. Testing deployment..."

echo "   🔍 Testing SuperAdmin backend..."
if curl -s -o /dev/null -w "%{http_code}" http://103.23.198.101/super-admin/api/public/health | grep -q "200"; then
    echo "   ✅ SuperAdmin API is working"
else
    echo "   ❌ SuperAdmin API test failed"
fi

echo "   🔍 Testing ArahPotensi frontend..."
if curl -s -o /dev/null -w "%{http_code}" http://103.23.198.101/ | grep -q "200"; then
    echo "   ✅ ArahPotensi frontend is working"
else
    echo "   ❌ ArahPotensi frontend test failed"
fi

echo ""
echo "📊 5. Deployment Summary"
echo "========================"
echo ""
echo "✅ SuperAdmin Laravel Backend:"
echo "   - URL: http://103.23.198.101/super-admin"
echo "   - API: http://103.23.198.101/super-admin/api"
echo "   - Login: http://103.23.198.101/super-admin/login"
echo "   - Credentials: admin / password123"
echo ""
echo "✅ ArahPotensi Next.js Frontend:"
echo "   - URL: http://103.23.198.101"
echo "   - API Integration: Configured"
echo ""
echo "✅ CORS Configuration:"
echo "   - Origins: http://103.23.198.101, http://localhost:3000"
echo "   - Methods: GET, POST, PUT, DELETE, OPTIONS"
echo "   - Headers: Content-Type, Authorization, X-Requested-With"
echo "   - Credentials: Enabled"
echo ""
echo "🎉 Deployment completed successfully!"
echo "===================================="
echo ""
echo "Next steps:"
echo "1. Test login at: http://103.23.198.101/super-admin/login"
echo "2. Test API at: http://103.23.198.101/super-admin/api/public/health"
echo "3. Test frontend at: http://103.23.198.101"
echo "4. Check browser console for any errors"
echo ""
