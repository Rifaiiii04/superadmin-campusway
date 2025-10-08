# Manual Fix for Production Server

## Problem
- CSS file `app-BXuoaa3o.css` returns 404
- JS file `app-ZrEbd4JQ.js` returns 404
- Blank white screen

## Solution

### Option 1: Automated Fix
1. Upload `production_server.zip` to your server
2. Extract it
3. Run: `chmod +x deploy.sh && ./deploy.sh`

### Option 2: Manual Fix
1. Upload all files from `assets/` folder to `/var/www/html/public/build/assets/`
2. Upload `manifest.json` to `/var/www/html/public/build/`
3. Set permissions:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/public/build/
   sudo chmod -R 755 /var/www/html/public/build/
   ```
4. Clear Laravel caches:
   ```bash
   cd /var/www/html
   sudo -u www-data php artisan config:clear
   sudo -u www-data php artisan cache:clear
   sudo -u www-data php artisan view:clear
   sudo -u www-data php artisan route:clear
   ```
5. Restart web server:
   ```bash
   sudo systemctl restart apache2
   # or
   sudo systemctl restart nginx
   ```

### Files Included
- `app-BXuoaa3o.css` ✅
- `app-ZrEbd4JQ.js` ✅
- `app-BmXzstTo.js` ✅
- `app-7QGUyAKp.js` ✅
- `app-B7z8JaNN.css` ✅
- `manifest.json` ✅

### Test
After deployment, test these URLs:
- `http://103.23.198.101:8080/build/assets/app-BXuoaa3o.css`
- `http://103.23.198.101:8080/build/assets/app-ZrEbd4JQ.js`

Both should return 200 OK status.
