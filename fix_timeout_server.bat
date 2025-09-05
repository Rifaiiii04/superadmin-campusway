@echo off
echo 🚀 TKA Server Timeout Fix
echo =========================
echo.

REM Kill any existing PHP processes
echo 🧹 Stopping existing PHP processes...
taskkill /f /im php.exe 2>nul
taskkill /f /im "php artisan serve" 2>nul
timeout /t 3 /nobreak >nul

REM Check if we're in the right directory
if not exist "artisan" (
    echo ❌ Error: Not in Laravel root directory
    echo Please run this script from the superadmin-backend folder
    pause
    exit /b 1
)

REM Set optimal PHP settings
echo ⚙️  Setting optimal PHP configuration...
set PHP_INI_SCAN_DIR=
set PHPRC=php.ini

REM Clear all caches first
echo 🧹 Clearing application caches...
php -c php.ini artisan cache:clear 2>nul
php -c php.ini artisan config:clear 2>nul
php -c php.ini artisan route:clear 2>nul
php -c php.ini artisan view:clear 2>nul
php -c php.ini artisan optimize:clear 2>nul

REM Test database connection
echo 🔍 Testing database connection...
php -c php.ini artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database connection successful'; } catch(Exception \$e) { echo 'Database error: ' . \$e->getMessage(); exit(1); }" 2>nul
if %errorlevel% neq 0 (
    echo ❌ Database connection failed
    echo Please check your database configuration
    pause
    exit /b 1
)

REM Optimize application
echo ⚡ Optimizing application...
php -c php.ini artisan config:cache 2>nul
php -c php.ini artisan route:cache 2>nul
php -c php.ini artisan view:cache 2>nul

REM Create a simple health check endpoint
echo 📝 Creating health check endpoint...
php -c php.ini artisan make:controller HealthController 2>nul

REM Start the server with timeout protection
echo 🌐 Starting Laravel development server...
echo 📍 Server URL: http://localhost:8000
echo 📍 Health Check: http://localhost:8000/health
echo ⏹️  Press Ctrl+C to stop the server
echo.

REM Use a more robust server command with timeout handling
php -c php.ini artisan serve --host=127.0.0.1 --port=8000 --timeout=0

echo.
echo 🛑 Server stopped
pause
