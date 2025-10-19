@echo off
echo Starting Laravel server on port 8080...
echo.

REM Change to the project directory
cd /d "%~dp0"

REM Start Laravel development server on port 8080
php artisan serve --host=0.0.0.0 --port=8080

echo.
echo Server started on http://0.0.0.0:8080
echo Press Ctrl+C to stop the server
pause
