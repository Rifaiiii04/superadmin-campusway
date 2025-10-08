#!/bin/bash

echo "🔧 Simple fix for blank white screen..."

# Fix permissions without sudo
echo "🔧 Step 1: Fixing permissions..."
chmod -R 775 storage/ 2>/dev/null || true
chmod -R 775 bootstrap/cache/ 2>/dev/null || true

# Clear all caches
echo "🔧 Step 2: Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Configure .env for development
echo "🔧 Step 3: Configuring .env..."
if [ -f ".env" ]; then
    # Backup original .env
    cp .env .env.backup
    
    # Set debug mode
    sed -i 's/APP_DEBUG=false/APP_DEBUG=true/' .env
    
    # Set local URL
    sed -i 's|APP_URL=http://103.23.198.101|APP_URL=http://localhost:8000|' .env
    
    echo "✅ .env configured for development"
    echo "APP_DEBUG=$(grep APP_DEBUG .env | cut -d'=' -f2)"
    echo "APP_URL=$(grep APP_URL .env | cut -d'=' -f2)"
else
    echo "❌ .env file not found!"
    exit 1
fi

# Rebuild assets
echo "🔧 Step 4: Rebuilding assets..."
npm run build

# Check if assets exist
echo "🔧 Step 5: Checking assets..."
if [ -f "public/build/assets/app-BXuoaa3o.css" ]; then
    echo "✅ CSS assets exist"
else
    echo "❌ CSS assets missing"
fi

if [ -f "public/build/assets/app-ZrEbd4JQ.js" ]; then
    echo "✅ JS assets exist"
else
    echo "❌ JS assets missing"
fi

echo ""
echo "✅ Fix completed!"
echo ""
echo "🌐 To test the application:"
echo "1. Run: php artisan serve --host=0.0.0.0 --port=8000"
echo "2. Open: http://localhost:8000"
echo "3. Or open: http://103.23.198.101:8000"
echo ""
echo "🔍 If still blank:"
echo "1. Check browser console for errors (F12)"
echo "2. Check if web server is pointing to 'public' directory"
echo "3. Try opening in incognito/private mode"
echo "4. Check if all required PHP extensions are installed"
