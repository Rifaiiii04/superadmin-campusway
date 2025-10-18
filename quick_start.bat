@echo off
echo Starting TKA Server...
taskkill /f /im php.exe 2>nul
php -c php.ini artisan serve --host=127.0.0.1 --port=8000
