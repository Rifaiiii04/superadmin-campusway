@echo off
echo ========================================
echo TKA SuperAdmin Timeout Fix Script
echo ========================================
echo.

echo [1/6] Menghentikan server yang berjalan...
taskkill /f /im php.exe 2>nul
timeout /t 2 /nobreak >nul

echo [2/6] Membersihkan cache Laravel...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo [3/6] Mengupdate konfigurasi database...
echo Konfigurasi timeout telah diupdate di config/database.php

echo [4/6] Testing koneksi database...
php test_connection.php

echo.
echo [5/6] Setup database (jika diperlukan)...
echo Apakah Anda ingin menjalankan setup database? (y/n)
set /p choice=
if /i "%choice%"=="y" (
    php setup_database.php
)

echo.
echo [6/6] Menjalankan server dengan konfigurasi baru...
echo Server akan berjalan di: http://localhost:8000
echo Tekan Ctrl+C untuk menghentikan server
echo.

php artisan serve --host=0.0.0.0 --port=8000

pause
