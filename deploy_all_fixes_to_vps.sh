#!/bin/bash

echo "🚀 Deploying All Fixes to VPS Production..."

# 1. Upload all controllers
echo "1. Uploading controllers..."
scp -r app/Http/Controllers/SchoolController.php root@your-vps-ip:/path/to/superadmin-backend/app/Http/Controllers/
scp -r app/Http/Controllers/MajorRecommendationController.php root@your-vps-ip:/path/to/superadmin-backend/app/Http/Controllers/
scp -r app/Http/Controllers/QuestionController.php root@your-vps-ip:/path/to/superadmin-backend/app/Http/Controllers/
scp -r app/Http/Controllers/ResultController.php root@your-vps-ip:/path/to/superadmin-backend/app/Http/Controllers/
scp -r app/Http/Controllers/TkaScheduleController.php root@your-vps-ip:/path/to/superadmin-backend/app/Http/Controllers/

# 2. Upload updated routes
echo "2. Uploading updated routes..."
scp -r routes/web.php root@your-vps-ip:/path/to/superadmin-backend/routes/

# 3. Upload updated models
echo "3. Uploading updated models..."
scp -r app/Models/School.php root@your-vps-ip:/path/to/superadmin-backend/app/Models/

# 4. Upload updated components
echo "4. Uploading updated components..."
scp -r resources/js/Pages/SuperAdmin/Schools.jsx root@your-vps-ip:/path/to/superadmin-backend/resources/js/Pages/SuperAdmin/
scp -r resources/js/Pages/SuperAdmin/MajorRecommendations.jsx root@your-vps-ip:/path/to/superadmin-backend/resources/js/Pages/SuperAdmin/

# 5. Upload database fix script
echo "5. Uploading database fix script..."
scp -r fix_vps_schools_database.php root@your-vps-ip:/path/to/superadmin-backend/

# 6. Run database fix on VPS
echo "6. Running database fix on VPS..."
ssh root@your-vps-ip "cd /path/to/superadmin-backend && php fix_vps_schools_database.php"

# 7. Clear cache and rebuild assets
echo "7. Clearing cache and rebuilding assets..."
ssh root@your-vps-ip "cd /path/to/superadmin-backend && php artisan config:clear && php artisan cache:clear && php artisan view:clear"

# 8. Rebuild frontend assets
echo "8. Rebuilding frontend assets..."
ssh root@your-vps-ip "cd /path/to/superadmin-backend && npm run build"

# 9. Restart services
echo "9. Restarting services..."
ssh root@your-vps-ip "systemctl restart apache2 && systemctl restart php8.1-fpm"

echo "✅ All fixes deployed successfully!"
echo "All pages should now work properly on VPS production."
