#!/bin/bash

# Production Deployment Script for TKA SuperAdmin Backend
echo "🚀 Starting production deployment..."

# Set production environment
export APP_ENV=production

# Navigate to project directory
cd "$(dirname "$0")/.."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from the Laravel project root."
    exit 1
fi

# Backup current version
echo "💾 Creating backup..."
BACKUP_DIR="backups/$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -r . "$BACKUP_DIR/" 2>/dev/null || echo "⚠️  Backup creation failed, continuing..."

# Pull latest changes
echo "📥 Pulling latest changes..."
git pull origin main

# Install/Update dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Clear and optimize caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run production optimization
echo "⚡ Running production optimization..."
php artisan optimize:production

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Clear and rebuild caches
echo "🔄 Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
echo "🔄 Restarting services..."
sudo systemctl reload nginx
sudo systemctl restart php8.1-fpm

# Health check
echo "🏥 Running health check..."
sleep 5
if curl -f http://localhost/super-admin/api/web/health > /dev/null 2>&1; then
    echo "✅ Health check passed!"
else
    echo "❌ Health check failed!"
    echo "🔍 Checking logs..."
    tail -n 20 storage/logs/laravel.log
    exit 1
fi

# Performance check
echo "📊 Running performance check..."
RESPONSE_TIME=$(curl -o /dev/null -s -w '%{time_total}' http://localhost/super-admin/api/web/health)
if (( $(echo "$RESPONSE_TIME < 2.0" | bc -l) )); then
    echo "✅ Performance check passed! Response time: ${RESPONSE_TIME}s"
else
    echo "⚠️  Performance warning: Response time: ${RESPONSE_TIME}s"
fi

# Cleanup old backups (keep last 5)
echo "🧹 Cleaning up old backups..."
ls -t backups/ | tail -n +6 | xargs -I {} rm -rf "backups/{}"

echo "🎉 Production deployment completed successfully!"
echo "📊 Deployment Summary:"
echo "   - Environment: Production"
echo "   - Backup: $BACKUP_DIR"
echo "   - Response Time: ${RESPONSE_TIME}s"
echo "   - Status: ✅ Healthy"
