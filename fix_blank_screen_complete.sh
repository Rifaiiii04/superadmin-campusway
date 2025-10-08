#!/bin/bash

echo "🔧 Complete fix for blank white screen..."

# Fix permissions
echo "🔧 Step 1: Fixing permissions..."
sudo chown -R www-data:www-data storage/ bootstrap/cache/ 2>/dev/null || true
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Clear all caches with proper permissions
echo "🔧 Step 2: Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Fix .env for development
echo "🔧 Step 3: Configuring .env for development..."
if [ -f ".env" ]; then
    # Set debug mode
    sed -i 's/APP_DEBUG=false/APP_DEBUG=true/' .env
    
    # Set local URL
    sed -i 's|APP_URL=http://103.23.198.101|APP_URL=http://localhost:8000|' .env
    
    # Set database to sqlite for testing
    sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
    sed -i 's/DB_DATABASE=campusway_db/DB_DATABASE=database\/database.sqlite/' .env
    
    echo "✅ .env configured for development"
else
    echo "❌ .env file not found!"
    exit 1
fi

# Create SQLite database
echo "🔧 Step 4: Creating SQLite database..."
touch database/database.sqlite
chmod 664 database/database.sqlite

# Run migrations
echo "🔧 Step 5: Running migrations..."
php artisan migrate --force

# Rebuild assets
echo "🔧 Step 6: Rebuilding assets..."
npm run build

# Test the application
echo "🔧 Step 7: Testing application..."
echo "Starting Laravel development server..."
echo "Open http://localhost:8000 in your browser"
echo ""
echo "Press Ctrl+C to stop the server"

# Start the server
php artisan serve --host=0.0.0.0 --port=8000
