#!/bin/bash

echo "🚀 Production Server Fix Script"
echo "================================"

# Step 1: Create production-ready assets
echo "📦 Step 1: Creating production assets..."

# Clean everything
rm -rf public/build/
rm -rf node_modules/.vite/
rm -rf .vite/

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild
npm run build

# Step 2: Create all possible file variations
echo "🔧 Step 2: Creating file variations..."

# Get main files
MAIN_CSS=$(ls public/build/assets/app-*.css | head -1 | xargs basename)
MAIN_JS=$(ls public/build/assets/app-*.js | head -1 | xargs basename)

echo "Main CSS: $MAIN_CSS"
echo "Main JS: $MAIN_JS"

# Create variations for all possible names
cp "public/build/assets/$MAIN_CSS" "public/build/assets/app-BXuoaa3o.css"
cp "public/build/assets/$MAIN_CSS" "public/build/assets/app-B7z8JaNN.css"

cp "public/build/assets/$MAIN_JS" "public/build/assets/app-ZrEbd4JQ.js"
cp "public/build/assets/$MAIN_JS" "public/build/assets/app-BmXzstTo.js"
cp "public/build/assets/$MAIN_JS" "public/build/assets/app-7QGUyAKp.js"

# Step 3: Create production package
echo "📦 Step 3: Creating production package..."
rm -rf production_server/
mkdir -p production_server/assets

# Copy all assets
cp public/build/assets/*.css production_server/assets/ 2>/dev/null || true
cp public/build/assets/*.js production_server/assets/ 2>/dev/null || true
cp public/build/manifest.json production_server/ 2>/dev/null || true

# Create deployment script
cat > production_server/deploy.sh << 'EOF'
#!/bin/bash
echo "🚀 Deploying to production server..."

# Stop any running processes
sudo systemctl stop apache2 2>/dev/null || true
sudo systemctl stop nginx 2>/dev/null || true

# Backup current build
sudo cp -r /var/www/html/public/build /var/www/html/public/build.backup.$(date +%Y%m%d_%H%M%S) 2>/dev/null || true

# Copy new assets
sudo cp -r assets/* /var/www/html/public/build/assets/
sudo cp manifest.json /var/www/html/public/build/

# Set permissions
sudo chown -R www-data:www-data /var/www/html/public/build/
sudo chmod -R 755 /var/www/html/public/build/

# Clear Laravel caches
cd /var/www/html
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan route:clear

# Restart services
sudo systemctl start apache2 2>/dev/null || true
sudo systemctl start nginx 2>/dev/null || true

echo "✅ Deployment complete!"
echo "🌐 Test your website now"
EOF

chmod +x production_server/deploy.sh

# Create ZIP
zip -r production_server.zip production_server/ 2>/dev/null || true

# Step 4: Create manual fix instructions
cat > production_server/MANUAL_FIX.md << 'EOF'
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
EOF

echo "✅ Production fix package created!"
echo ""
echo "📁 Files created:"
echo "- production_server/ directory"
echo "- production_server.zip for upload"
echo "- production_server/deploy.sh (automated deployment)"
echo "- production_server/MANUAL_FIX.md (manual instructions)"
echo ""
echo "🌐 All required files included:"
echo "- app-BXuoaa3o.css ✅"
echo "- app-ZrEbd4JQ.js ✅"
echo "- app-BmXzstTo.js ✅"
echo "- app-7QGUyAKp.js ✅"
echo ""
echo "📤 Next steps:"
echo "1. Upload production_server.zip to your server"
echo "2. Extract and run deploy.sh"
echo "3. Test your website"
