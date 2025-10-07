#!/bin/bash

# Configuration
VPS_USER="root"
VPS_IP="your-vps-ip" # Ganti dengan IP VPS Anda
PROJECT_PATH="/path/to/superadmin-backend" # Ganti dengan path proyek di VPS

echo "🚀 Starting complete deployment of all fixes to VPS..."

# 1. Copy all updated files to VPS
echo "📁 Copying updated files to VPS..."

# Copy controllers
scp app/Http/Controllers/SchoolController.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Http/Controllers/
scp app/Http/Controllers/StudentController.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Http/Controllers/
scp app/Http/Controllers/MajorRecommendationController.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Http/Controllers/
scp app/Http/Controllers/QuestionController.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Http/Controllers/
scp app/Http/Controllers/ResultController.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Http/Controllers/
scp app/Http/Controllers/TkaScheduleController.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Http/Controllers/

# Copy models
scp app/Models/School.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Models/
scp app/Models/Student.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Models/
scp app/Models/MajorRecommendation.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Models/
scp app/Models/RumpunIlmu.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Models/
scp app/Models/ProgramStudi.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Models/
scp app/Models/ProgramStudiSubject.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Models/
scp app/Models/MajorSubjectMapping.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Models/
scp app/Models/StudentChoice.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Models/
scp app/Models/StudentSubject.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Models/
scp app/Models/SchoolClass.php $VPS_USER@$VPS_IP:$PROJECT_PATH/app/Models/

# Copy routes
scp routes/web.php $VPS_USER@$VPS_IP:$PROJECT_PATH/routes/

# Copy frontend components
scp resources/js/Pages/SuperAdmin/Schools.jsx $VPS_USER@$VPS_IP:$PROJECT_PATH/resources/js/Pages/SuperAdmin/
scp resources/js/Pages/SuperAdmin/Students.jsx $VPS_USER@$VPS_IP:$PROJECT_PATH/resources/js/Pages/SuperAdmin/
scp resources/js/Pages/SuperAdmin/MajorRecommendations.jsx $VPS_USER@$VPS_IP:$PROJECT_PATH/resources/js/Pages/SuperAdmin/

# Copy migration
scp database/migrations/2025_01_15_000000_fix_schools_password_field.php $VPS_USER@$VPS_IP:$PROJECT_PATH/database/migrations/

if [ $? -ne 0 ]; then
    echo "❌ Error: Failed to copy files to VPS."
    exit 1
fi

echo "✅ Files copied successfully."

# 2. Run commands on VPS
echo "🔧 Running commands on VPS..."

ssh $VPS_USER@$VPS_IP "cd $PROJECT_PATH && bash -c '
    echo \"📦 Running composer install...\"
    composer install --no-dev --optimize-autoloader
    
    echo \"🗄️ Running database migrations...\"
    php artisan migrate --force
    
    echo \"🔨 Building frontend assets...\"
    npm install
    npm run build
    
    echo \"🧹 Clearing caches...\"
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
    
    echo \"🔧 Optimizing application...\"
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    echo \"🔄 Restarting services...\"
    systemctl restart apache2
    systemctl restart php8.1-fpm
    
    echo \"✅ All commands completed successfully.\"
'"

if [ $? -ne 0 ]; then
    echo "❌ Error: Failed to execute commands on VPS."
    exit 1
fi

echo "🎉 Complete deployment finished successfully!"
echo ""
echo "📋 Summary of deployed fixes:"
echo "✅ Schools page - Fixed data handling and added CRUD operations"
echo "✅ Students page - Added complete CRUD operations"
echo "✅ Major Recommendations - Updated to match database structure"
echo "✅ Questions, Results, TKA Schedules - Added controllers and routes"
echo "✅ Database models - Updated to match actual database structure"
echo "✅ Frontend components - Fixed data handling and pagination"
echo "✅ Database migration - Fixed schools table password field"
echo ""
echo "🌐 Your application should now be working properly on the VPS!"
echo "🔗 Check your VPS URL to verify the fixes are working."
