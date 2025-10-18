@echo off
echo Starting SQL Server Express...
echo.

REM Coba start SQL Server Express service
net start "MSSQL$SQLEXPRESS"

if %errorlevel% == 0 (
    echo SQL Server Express started successfully!
    echo.
    echo Now you can run: php artisan serve
) else (
    echo Failed to start SQL Server Express.
    echo.
    echo Please run this script as Administrator:
    echo 1. Right-click on this file
    echo 2. Select "Run as administrator"
    echo 3. Try again
)

echo.
pause
