@echo off
echo 🚀 Starting Optimized Laravel Server...
echo.

REM Set PHP optimizations
set PHP_INI_SCAN_DIR=
php -d memory_limit=512M -d max_execution_time=300 -d opcache.enable=1 -d opcache.memory_consumption=128 -d opcache.max_accelerated_files=4000

REM Clear caches and optimize
echo 🧹 Optimizing server...
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

REM Start server with optimized settings
echo 🌐 Starting server on http://localhost:8000
echo.
php artisan serve --host=0.0.0.0 --port=8000

pause