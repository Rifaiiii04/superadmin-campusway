# Production Deployment Instructions

## Files Included
- app-BXuoaa3o.css ✅
- app-ZrEbd4JQ.js ✅
- app-BmXzstTo.js ✅
- app-7QGUyAKp.js ✅
- app-B7z8JaNN.css ✅
- manifest.json ✅

## Deployment Steps

### Option 1: Quick Fix
1. Upload all files from `assets/` folder to `/var/www/html/public/build/assets/`
2. Upload `manifest.json` to `/var/www/html/public/build/`
3. Set permissions: `sudo chown -R www-data:www-data /var/www/html/public/build/`
4. Clear cache: `sudo -u www-data php artisan config:clear`

### Option 2: Complete Deployment
1. Upload entire project to server
2. Run: `composer install --no-dev --optimize-autoloader`
3. Run: `npm run build`
4. Set permissions: `sudo chown -R www-data:www-data /var/www/html/`
5. Clear caches: `sudo -u www-data php artisan config:clear`

## Test URLs
- http://103.23.198.101:8080/build/assets/app-BXuoaa3o.css
- http://103.23.198.101:8080/build/assets/app-ZrEbd4JQ.js

Both should return 200 OK status.
