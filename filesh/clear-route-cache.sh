#!/bin/bash

echo "🧹 Clearing Laravel Route Cache..."
echo "=================================="

cd "$(dirname "$0")/.." || exit 1

# Check if artisan exists
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from superadmin-backend directory"
    exit 1
fi

echo "📍 Current directory: $(pwd)"
echo ""

# Clear route cache
echo "1️⃣ Clearing route cache..."
php artisan route:clear
echo "✅ Route cache cleared"
echo ""

# Clear config cache
echo "2️⃣ Clearing config cache..."
php artisan config:clear
echo "✅ Config cache cleared"
echo ""

# Clear application cache
echo "3️⃣ Clearing application cache..."
php artisan cache:clear
echo "✅ Application cache cleared"
echo ""

# List routes to verify
echo "4️⃣ Listing DELETE routes for students..."
php artisan route:list --path=school/students --method=DELETE
echo ""

echo "=================================="
echo "✅ All caches cleared successfully!"
echo ""
echo "📋 Next steps:"
echo "   1. Test the DELETE endpoint again"
echo "   2. Check Laravel logs: tail -f storage/logs/laravel.log"
echo "   3. Check middleware logs: tail -f storage/logs/middleware_debug.log"

