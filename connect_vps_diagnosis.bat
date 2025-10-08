@echo off
echo Connecting to VPS for diagnosis...
echo.

REM Connect to VPS
ssh marketing@103.23.198.101

echo.
echo After connecting to VPS, run these commands:
echo.
echo 1. cd /var/www/superadmin/superadmin-campusway
echo 2. php artisan tinker
echo.
echo Then in tinker, run:
echo DB::table('schools')->count();
echo DB::table('students')->count();
echo DB::table('major_recommendations')->count();
echo App\Models\School::count();
echo App\Models\Student::count();
echo exit
echo.
echo Or run: php debug_production_data.php
echo.
pause
