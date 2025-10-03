@echo off
echo ========================================
echo TKA SuperAdmin Server Restart Script
echo ========================================
echo.

echo [1/4] Menghentikan server yang berjalan...
taskkill /f /im php.exe 2>nul
timeout /t 2 /nobreak >nul

echo [2/4] Membersihkan cache Laravel...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo [3/4] Testing koneksi database...
php test_connection.php

echo.
echo [4/4] Menjalankan server...
echo Server akan berjalan di: http://localhost:8000
echo Tekan Ctrl+C untuk menghentikan server
echo.
php artisan serve --host=0.0.0.0 --port=8000

pause
