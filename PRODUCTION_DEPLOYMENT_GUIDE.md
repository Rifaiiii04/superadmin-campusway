# TKA Super Admin - Production Deployment Guide

## 🚀 Overview

Panduan lengkap untuk deployment aplikasi TKA Super Admin ke production server.

## 📋 Prerequisites

-   VPS dengan Ubuntu 20.04+ atau CentOS 8+
-   Apache 2.4+
-   PHP 8.3+ dengan FPM
-   Node.js 18+
-   Composer
-   Git
-   SQL Server Express

## 🔧 Server Configuration

### 1. Apache Virtual Host Configuration

```apache
<VirtualHost *:80>
    ServerName 103.23.198.101
    DocumentRoot /var/www/html

    # TKA Frontend (Next.js) - Root
    <Directory /var/www/html>
        AllowOverride All
        Require all granted
    </Directory>

    # TKA Super Admin (Laravel) - Subfolder
    Alias /super-admin /var/www/superadmin/superadmin-campusway/public

    <Directory /var/www/superadmin/superadmin-campusway/public>
        AllowOverride All
        Require all granted

        # Include the provided Apache config
        Include /var/www/superadmin/superadmin-campusway/apache-config.conf
    </Directory>

    # Logs
    ErrorLog ${APACHE_LOG_DIR}/tka_error.log
    CustomLog ${APACHE_LOG_DIR}/tka_access.log combined
</VirtualHost>
```

### 2. Environment Configuration

#### Laravel (.env)

```env
APP_NAME="TKA Super Admin"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=http://103.23.198.101/super-admin
ASSET_URL=http://103.23.198.101/super-admin

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=sqlsrv
DB_HOST=localhost
DB_PORT=1433
DB_DATABASE=tka_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=103.23.198.101

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

#### Next.js (.env.local)

```env
NEXT_PUBLIC_API_BASE_URL=http://103.23.198.101/super-admin/api/school
NEXT_PUBLIC_STUDENT_API_BASE_URL=http://103.23.198.101/super-admin/api/web
NEXT_PUBLIC_SUPERADMIN_API_URL=http://103.23.198.101/super-admin/api
NODE_ENV=production
```

## 🚀 Deployment Steps

### 1. Clone and Setup Laravel Backend

```bash
# Clone repository
cd /var/www
git clone <your-repo-url> superadmin/superadmin-campusway
cd superadmin/superadmin-campusway

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci --production

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database
# Edit .env with your database credentials

# Run migrations
php artisan migrate --force

# Build assets
npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/superadmin/superadmin-campusway
sudo chmod -R 755 /var/www/superadmin/superadmin-campusway
sudo chmod -R 775 storage bootstrap/cache
```

### 2. Setup Next.js Frontend

```bash
# Clone frontend repository
cd /var/www/html
git clone <frontend-repo-url> .

# Install dependencies
npm ci --production

# Copy environment file
cp .env.example .env.local

# Build for production
npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
```

### 3. Apache Configuration

```bash
# Copy Apache config
sudo cp /var/www/superadmin/superadmin-campusway/apache-config.conf /etc/apache2/sites-available/tka.conf

# Enable site
sudo a2ensite tka.conf

# Enable required modules
sudo a2enmod rewrite headers deflate expires

# Restart Apache
sudo systemctl restart apache2
```

## 🔍 Verification

### 1. Test URLs

-   Frontend: http://103.23.198.101
-   Super Admin: http://103.23.198.101/super-admin
-   API Health: http://103.23.198.101/super-admin/api/optimized/health

### 2. Check MIME Types

```bash
curl -I http://103.23.198.101/super-admin/build/assets/app.js
# Should return: Content-Type: application/javascript
```

### 3. Test API Endpoints

```bash
# Test school API
curl http://103.23.198.101/super-admin/api/web/schools

# Test major API
curl http://103.23.198.101/super-admin/api/web/majors
```

## 🛠️ Troubleshooting

### Common Issues

#### 1. Double URL Issue

**Problem**: URLs like `/super-admin/super-admin/schools`
**Solution**: ✅ Fixed - All hardcoded URLs replaced with relative paths

#### 2. MIME Type Error

**Problem**: JavaScript files served as `text/html`
**Solution**: ✅ Fixed - Added MIME type headers in Apache config

#### 3. 404 on Edit Password

**Problem**: Edit password endpoint returns 404
**Solution**: ✅ Fixed - Updated routes to use relative paths

#### 4. API Connection Issues

**Problem**: Frontend can't connect to API
**Solution**: ✅ Fixed - Updated API endpoints to production URLs

### Debug Commands

```bash
# Check Apache error logs
sudo tail -f /var/log/apache2/error.log

# Check Laravel logs
tail -f /var/www/superadmin/superadmin-campusway/storage/logs/laravel.log

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

## 📊 Performance Optimization

### 1. Laravel Optimizations

```bash
# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### 2. Apache Optimizations

-   Enable mod_deflate for compression
-   Set proper cache headers
-   Enable mod_expires for static assets

### 3. Database Optimizations

-   Add proper indexes
-   Use connection pooling
-   Enable query caching

## 🔒 Security Considerations

### 1. File Permissions

```bash
# Secure file permissions
sudo chmod 644 /var/www/superadmin/superadmin-campusway/.env
sudo chmod 755 /var/www/superadmin/superadmin-campusway
sudo chmod -R 775 storage bootstrap/cache
```

### 2. Apache Security Headers

-   X-Content-Type-Options: nosniff
-   X-Frame-Options: DENY
-   X-XSS-Protection: 1; mode=block

### 3. CORS Configuration

-   Proper CORS headers for API access
-   Restricted origins in production

## 📈 Monitoring

### 1. Health Checks

-   API health endpoint: `/api/optimized/health`
-   Database connection check
-   File system permissions check

### 2. Log Monitoring

-   Apache access/error logs
-   Laravel application logs
-   PHP-FPM logs

### 3. Performance Metrics

-   Response times
-   Memory usage
-   Database query performance

## 🔄 Updates and Maintenance

### 1. Code Updates

```bash
# Run deployment script
chmod +x deploy-production.sh
./deploy-production.sh
```

### 2. Database Updates

```bash
# Run migrations
php artisan migrate --force

# Seed data if needed
php artisan db:seed --force
```

### 3. Asset Updates

```bash
# Rebuild assets
npm run build

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## ✅ Final Checklist

-   [ ] All hardcoded URLs replaced with relative paths
-   [ ] MIME types properly configured
-   [ ] API endpoints working correctly
-   [ ] Database connection established
-   [ ] File permissions set correctly
-   [ ] Apache modules enabled
-   [ ] SSL certificate installed (if using HTTPS)
-   [ ] Monitoring setup
-   [ ] Backup strategy implemented
-   [ ] Security headers configured

## 📞 Support

Jika mengalami masalah, periksa:

1. Apache error logs
2. Laravel application logs
3. Database connection
4. File permissions
5. Network connectivity

---

**Status**: ✅ Production Ready
**Last Updated**: $(date)
**Version**: 1.0.0
