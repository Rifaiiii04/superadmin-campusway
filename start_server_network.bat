@echo off
echo Starting Laravel server for network access...
cd /d "E:\TKAWEB Project\code\superadmin-backend"
php artisan serve --host=0.0.0.0 --port=8000
pause
