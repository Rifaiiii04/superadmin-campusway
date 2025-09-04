@echo off
echo ========================================
echo    Student Choice Seeder Runner (Optimized)
echo ========================================
echo.

echo [1/4] Running Optimized Seeder...
php -c php.ini optimized_seeder.php

if %errorlevel% neq 0 (
    echo.
    echo ❌ Optimized seeder failed. Trying Laravel seeder...
    echo.
    echo [2/4] Running Laravel Seeder with timeout...
    php -d max_execution_time=300 -d memory_limit=1024M artisan db:seed --class=StudentChoiceSeeder
    
    if %errorlevel% neq 0 (
        echo.
        echo ❌ Laravel seeder failed. Trying manual script...
        echo.
        echo [3/4] Running Manual PHP Script...
        php -d max_execution_time=300 -d memory_limit=1024M run_student_choice_seeder.php
        
        if %errorlevel% neq 0 (
            echo.
            echo ❌ Manual script also failed. Please check the database connection.
            echo.
            echo [4/4] You can also run the SQL script manually:
            echo - Open SQL Server Management Studio
            echo - Connect to your database
            echo - Run the file: seed_student_choices.sql
            pause
            exit /b 1
        )
    )
) else (
    echo.
    echo ✅ Optimized seeder completed successfully!
)

echo.
echo ========================================
echo    Seeder completed successfully!
echo ========================================
echo.
echo You can now test the major selection feature:
echo 1. Login as a student
echo 2. Check if the major status is displayed correctly
echo 3. Try selecting/changing majors
echo.
pause
