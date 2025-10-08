#!/bin/bash

echo "Fixing 404 assets error..."

# Clear Laravel caches
echo "Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild assets
echo "Rebuilding assets..."
npm run build

# Set proper permissions
echo "Setting proper permissions..."
chmod -R 755 public/build/
chown -R www-data:www-data public/build/ 2>/dev/null || true

# Clear browser cache instructions
echo ""
echo "✅ Assets rebuilt successfully!"
echo ""
echo "To fix the 404 errors, please:"
echo "1. Hard refresh your browser (Ctrl+F5 or Cmd+Shift+R)"
echo "2. Clear browser cache completely"
echo "3. Or open the site in incognito/private mode"
echo ""
echo "The new assets are:"
echo "- CSS: app-BXuoaa3o.css (53.52 kB)"
echo "- JS: app-BmXzstTo.js (293.98 kB)"
echo ""
echo "If the problem persists, check your web server configuration"
echo "to ensure it's serving files from the public/build directory correctly."
