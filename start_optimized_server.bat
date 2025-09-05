@echo off
echo 🚀 Starting Optimized Laravel Server...
echo.

REM Kill any existing PHP processes
echo 🧹 Stopping existing PHP processes...
taskkill /f /im php.exe 2>nul
timeout /t 2 /nobreak >nul

REM Set PHP optimizations using the custom php.ini
echo ⚙️  Applying PHP optimizations...
set PHP_INI_SCAN_DIR=
php -c php.ini -d memory_limit=1024M -d max_execution_time=0 -d max_input_time=300 -d opcache.enable=1 -d opcache.memory_consumption=256 -d opcache.max_accelerated_files=20000

REM Clear caches and optimize
echo 🧹 Optimizing server...
php -c php.ini artisan optimize:clear
php -c php.ini artisan config:cache
php -c php.ini artisan route:cache
php -c php.ini artisan view:cache

REM Check database connection
echo 🔍 Checking database connection...
php -c php.ini artisan tinker --execute="try { DB::select('SELECT 1'); echo 'Database OK'; } catch(Exception \$e) { echo 'Database Error: ' . \$e->getMessage(); }" 2>nul

REM Start server with optimized settings
echo 🌐 Starting server on http://localhost:8000
echo 📍 Health check: http://localhost:8000/api/optimized/health
echo ⏹️  Press Ctrl+C to stop the server
echo.
php -c php.ini artisan serve --host=0.0.0.0 --port=8000

pause