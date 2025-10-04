# 🚀 SuperAdmin 500 Error Fix - Deployment Guide

## 📋 Overview
This guide contains fixes for the SuperAdmin 500 Internal Server Error issues that were occurring due to problematic middleware and Vite configuration.

## 🔧 What Was Fixed

### 1. **Middleware Issues**
- Disabled `PerformanceOptimization` middleware (causing 500 error)
- Disabled `RequestTimeout` middleware (causing 500 error)  
- Disabled `AddLinkHeadersForPreloadedAssets` middleware (causing Vite manifest error)

### 2. **Vite Configuration**
- Fixed `app.blade.php` to use manual asset loading in production
- Added conditional Vite loading (production vs development)
- Ensured proper base path configuration

### 3. **Asset Management**
- Rebuilt all Vite assets with correct permissions
- Created `manifest.json` if missing
- Set proper file permissions for web server

## 📁 Files Modified

### Core Files:
- `app/Http/Kernel.php` - Disabled problematic middleware
- `resources/views/app.blade.php` - Fixed Vite loading for production
- `vite.config.js` - Enhanced configuration
- `routes/web.php` - Added missing Auth import

### New Scripts:
- `comprehensive-fix-500-error.sh` - Complete fix script
- `fix-middleware-500-error.sh` - Middleware-specific fix
- `fix-assets-500-error.sh` - Asset-specific fix
- `deploy-fix-500-error.sh` - Deployment script

## 🚀 Deployment Instructions

### Option 1: Comprehensive Fix (Recommended)
```bash
# On VPS, run:
cd /var/www/superadmin/superadmin-campusway
chmod +x comprehensive-fix-500-error.sh
./comprehensive-fix-500-error.sh
```

### Option 2: Step-by-Step Fix
```bash
# 1. Pull latest changes
git pull origin main

# 2. Fix middleware
chmod +x fix-middleware-500-error.sh
./fix-middleware-500-error.sh

# 3. Fix assets
chmod +x fix-assets-500-error.sh
./fix-assets-500-error.sh

# 4. Deploy
chmod +x deploy-fix-500-error.sh
./deploy-fix-500-error.sh
```

### Option 3: Manual Fix
```bash
# 1. Pull changes
git pull origin main

# 2. Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 3. Install dependencies
composer install --no-dev --optimize-autoloader

# 4. Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize:clear

# 5. Rebuild assets
npm run build

# 6. Set asset permissions
sudo chown -R www-data:www-data public
sudo chmod -R 755 public

# 7. Restart Apache
sudo systemctl restart apache2
```

## 🧪 Testing

After deployment, test these endpoints:

1. **Test Route**: `http://103.23.198.101/super-admin/test`
   - Should return: `{"status":"OK","message":"Routes working"}`

2. **Login Page**: `http://103.23.198.101/super-admin/login`
   - Should return: HTTP 200 OK (not 500)

3. **Assets**: 
   - `http://103.23.198.101/super-admin/build/assets/app-D_7II1BX.js`
   - `http://103.23.198.101/super-admin/build/assets/app-BHSs9Ase.css`
   - Should return: HTTP 200 OK

## 👤 Login Credentials

- **Username**: `admin`
- **Password**: `password123`

## 🔍 Troubleshooting

### If still getting 500 error:
1. Check Laravel logs: `tail -100 storage/logs/laravel.log`
2. Check Apache logs: `sudo tail -50 /var/log/apache2/error.log`
3. Verify file permissions: `ls -la storage/ bootstrap/cache/ public/build/`
4. Test individual components:
   - Routes: `php artisan route:list`
   - Config: `php artisan config:show`
   - Assets: `ls -la public/build/`

### If assets not loading:
1. Check manifest.json: `cat public/build/manifest.json`
2. Verify asset files exist: `ls -la public/build/assets/`
3. Check Apache alias configuration
4. Rebuild assets: `npm run build`

## 📝 Notes

- All scripts are designed to be safe and include error handling
- Scripts will backup important files before making changes
- The comprehensive fix script includes all necessary steps
- Manual asset loading is used in production to avoid Vite manifest issues

## ✅ Success Indicators

After successful deployment, you should see:
- ✅ SuperAdmin login page loads without 500 error
- ✅ Assets (JS/CSS) load correctly
- ✅ Login functionality works
- ✅ Dashboard loads after successful login
- ✅ No middleware-related errors in logs

---

**Created**: $(date)
**Purpose**: Fix SuperAdmin 500 Internal Server Error
**Status**: Ready for deployment
