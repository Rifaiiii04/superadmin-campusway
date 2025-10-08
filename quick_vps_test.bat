@echo off
echo ========================================
echo VPS DIAGNOSIS - QUICK TEST
echo ========================================
echo.
echo VPS Details:
echo Host: 103.23.198.101
echo User: marketing
echo Password: ##&21dq21)gZ
echo.
echo ========================================
echo STEP 1: Connect to VPS
echo ========================================
echo Run this command:
echo ssh marketing@103.23.198.101
echo.
echo ========================================
echo STEP 2: Quick Database Test
echo ========================================
echo After connecting, run these commands:
echo.
echo cd /var/www/superadmin/superadmin-campusway
echo php artisan tinker
echo.
echo Then in tinker:
echo DB::table('schools')->count();
echo DB::table('students')->count();
echo DB::table('major_recommendations')->count();
echo App\Models\School::count();
echo App\Models\Student::count();
echo exit
echo.
echo ========================================
echo STEP 3: Test API
echo ========================================
echo curl http://localhost/api/web/health
echo curl http://localhost/api/web/schools
echo curl http://localhost/api/web/majors
echo.
echo ========================================
echo STEP 4: Check Logs
echo ========================================
echo tail -f storage/logs/laravel.log
echo.
echo ========================================
echo STEP 5: Clear Cache
echo ========================================
echo php artisan cache:clear
echo php artisan config:clear
echo php artisan route:clear
echo php artisan view:clear
echo.
echo ========================================
echo STEP 6: Check Environment
echo ========================================
echo cat .env ^| grep DB_
echo.
echo ========================================
echo STEP 7: Check File Permissions
echo ========================================
echo ls -la storage/
echo ls -la bootstrap/cache/
echo.
echo ========================================
echo COPY THIS DEBUG SCRIPT TO VPS
echo ========================================
echo.
echo Create file: nano debug_simple.php
echo.
echo Then paste this content:
echo.
echo ^<?php
echo require_once 'vendor/autoload.php';
echo $app = require_once 'bootstrap/app.php';
echo $app-^>make('Illuminate\Contracts\Console\Kernel')-^>bootstrap();
echo echo "=== QUICK DIAGNOSIS ===\n";
echo try {
echo   $connection = \Illuminate\Support\Facades\DB::connection();
echo   echo "DB Connected: " . $connection-^>getDatabaseName() . "\n";
echo   echo "Schools: " . \Illuminate\Support\Facades\DB::table('schools')-^>count() . "\n";
echo   echo "Students: " . \Illuminate\Support\Facades\DB::table('students')-^>count() . "\n";
echo   echo "Majors: " . \Illuminate\Support\Facades\DB::table('major_recommendations')-^>count() . "\n";
echo   echo "Questions: " . \Illuminate\Support\Facades\DB::table('questions')-^>count() . "\n";
echo } catch (Exception $e) {
echo   echo "ERROR: " . $e-^>getMessage() . "\n";
echo }
echo echo "=== END ===\n";
echo ?^>
echo.
echo Then run: php debug_simple.php
echo.
echo ========================================
pause
