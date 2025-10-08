@echo off
echo === DEPLOYING ALL CONTROLLERS FIX TO VPS ===

REM Set VPS details
set VPS_USER=marketing
set VPS_HOST=103.23.198.101
set VPS_PATH=/var/www/superadmin/superadmin-campusway

echo 1. Copying files to VPS...

REM Copy all controller files
scp app/Http/Controllers/SchoolController.php %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/
scp app/Http/Controllers/MajorRecommendationController.php %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/
scp app/Http/Controllers/StudentController.php %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/
scp app/Http/Controllers/QuestionController.php %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/
scp app/Http/Controllers/ResultController.php %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/
scp app/Http/Controllers/SuperAdmin/TkaScheduleController.php %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/SuperAdmin/

REM Copy routes file
scp routes/web.php %VPS_USER%@%VPS_HOST%:%VPS_PATH%/routes/

REM Copy test script
scp test_all_controllers_vps.php %VPS_USER%@%VPS_HOST%:%VPS_PATH%/

echo 2. Running commands on VPS...

REM Run commands on VPS
ssh %VPS_USER%@%VPS_HOST% "cd /var/www/superadmin/superadmin-campusway && php artisan config:cache && php artisan route:cache && php artisan view:cache && npm run build && sudo chown -R www-data:www-data storage bootstrap/cache && sudo chmod -R 775 storage bootstrap/cache && sudo systemctl restart apache2 && php test_all_controllers_vps.php"

echo ✅ All controllers fix deployed successfully!
pause
