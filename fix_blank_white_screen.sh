#!/bin/bash

echo "🔍 Diagnosing blank white screen issue..."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Not in Laravel project directory"
    exit 1
fi

echo "📁 Current directory: $(pwd)"
echo "📋 Laravel version:"
php artisan --version

echo ""
echo "🔧 Step 1: Clear all caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo ""
echo "🔧 Step 2: Check file permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/

echo ""
echo "🔧 Step 3: Check .env file..."
if [ -f ".env" ]; then
    echo "✅ .env file exists"
    echo "APP_DEBUG=$(grep APP_DEBUG .env | cut -d'=' -f2)"
    echo "APP_URL=$(grep APP_URL .env | cut -d'=' -f2)"
else
    echo "❌ .env file not found!"
    echo "Creating .env from .env.example..."
    cp .env.example .env
    php artisan key:generate
fi

echo ""
echo "🔧 Step 4: Check database connection..."
php artisan migrate:status 2>/dev/null || echo "⚠️  Database connection issue"

echo ""
echo "🔧 Step 5: Rebuild assets..."
npm run build

echo ""
echo "🔧 Step 6: Check web server configuration..."
echo "Make sure your web server is pointing to the 'public' directory"
echo "For Apache: DocumentRoot should be /path/to/project/public"
echo "For Nginx: root should be /path/to/project/public;"

echo ""
echo "🔧 Step 7: Test basic routes..."
echo "Testing home route..."
curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/ || echo "Failed to connect"

echo ""
echo "✅ Diagnosis complete!"
echo ""
echo "📋 Common causes of blank white screen:"
echo "1. Missing .env file or wrong APP_DEBUG setting"
echo "2. File permission issues (storage/, bootstrap/cache/)"
echo "3. Web server not pointing to 'public' directory"
echo "4. Missing assets (CSS/JS files)"
echo "5. PHP errors (check error logs)"
echo "6. Database connection issues"
echo ""
echo "🔍 To debug further:"
echo "1. Check browser console for JavaScript errors"
echo "2. Check web server error logs"
echo "3. Set APP_DEBUG=true in .env file"
echo "4. Check if all required PHP extensions are installed"
