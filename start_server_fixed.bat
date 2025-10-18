@echo off
echo 🚀 Starting TKA Server (Fixed Version)
echo =====================================
echo.

REM Kill any existing PHP processes
echo 🧹 Stopping existing PHP processes...
taskkill /f /im php.exe 2>nul
timeout /t 2 /nobreak >nul

REM Set optimal PHP settings
echo ⚙️  Setting optimal PHP configuration...
set PHP_INI_SCAN_DIR=
set PHPRC=php.ini

REM Clear caches
echo 🧹 Clearing application caches...
php -c php.ini artisan cache:clear 2>nul
php -c php.ini artisan config:clear 2>nul
php -c php.ini artisan route:clear 2>nul
php -c php.ini artisan view:clear 2>nul

REM Test database connection
echo 🔍 Testing database connection...
php -c php.ini artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database OK'; } catch(Exception \$e) { echo 'Database Error: ' . \$e->getMessage(); }" 2>nul

REM Start server with timeout protection
echo 🌐 Starting Laravel development server...
echo 📍 Server URL: http://127.0.0.1:8000
echo 📍 TKA Login: http://127.0.0.1:8000/super-admin/login
echo ⏹️  Press Ctrl+C to stop the server
echo.

REM Use a more robust server command
php -c php.ini artisan serve --host=127.0.0.1 --port=8000 --timeout=0

echo.
echo 🛑 Server stopped
pause
