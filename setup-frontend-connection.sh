#!/bin/bash

# Setup script untuk menghubungkan frontend dengan backend
echo "🔧 Setting up Frontend-Backend Connection..."

# 1. Install dependencies jika belum ada
echo "📦 Installing dependencies..."
if [ ! -d "vendor" ]; then
    composer install --no-dev --optimize-autoloader
fi

if [ ! -d "node_modules" ]; then
    npm install
fi

# 2. Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# 3. Clear and cache config
echo "🗑️ Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 4. Cache configuration
echo "💾 Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/

# 6. Create .env file if not exists
if [ ! -f ".env" ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env 2>/dev/null || cat > .env << 'EOF'
APP_NAME="Campusway Superadmin"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://103.23.198.101/super-admin

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlsrv
DB_HOST=localhost
DB_PORT=1433
DB_DATABASE=campusway_db
DB_USERNAME=sa
DB_PASSWORD=your-password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# CORS Configuration
CORS_ALLOWED_ORIGINS="http://localhost:3000,http://127.0.0.1:3000,http://103.23.198.101"
CORS_ALLOWED_METHODS="GET,POST,PUT,DELETE,OPTIONS"
CORS_ALLOWED_HEADERS="Content-Type,Authorization,X-Requested-With,Accept,Origin"
CORS_SUPPORTS_CREDENTIALS=true
EOF
    php artisan key:generate
fi

# 7. Update CORS configuration
echo "🌐 Updating CORS configuration..."
cat > config/cors.php << 'EOF'
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://103.23.198.101',
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'http://localhost:3001',
        'http://127.0.0.1:3001',
        'https://localhost:3000',
        'https://127.0.0.1:3000',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => ['Content-Disposition', 'Content-Type', 'Content-Length'],
    'max_age' => 0,
    'supports_credentials' => true,
];
EOF

# 8. Test database connection
echo "🗄️ Testing database connection..."
php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'Database connection: SUCCESS';
} catch (Exception \$e) {
    echo 'Database connection: FAILED - ' . \$e->getMessage();
}
"

# 9. Run migrations if needed
echo "📊 Running migrations..."
php artisan migrate --force

# 10. Start server
echo "🚀 Starting server..."
echo "Backend will be available at: http://103.23.198.101/super-admin"
echo "API endpoints:"
echo "  - Health: http://103.23.198.101/super-admin/api/web/health"
echo "  - School Login: http://103.23.198.101/super-admin/api/school/login"
echo "  - Student Login: http://103.23.198.101/super-admin/api/web/login"
echo "  - Schools: http://103.23.198.101/super-admin/api/web/schools"
echo "  - Majors: http://103.23.198.101/super-admin/api/web/majors"

# Start PHP built-in server
php artisan serve --host=0.0.0.0 --port=8000

