#!/bin/bash

echo "🚀 Deploying Schools Fix to VPS Production..."

# 1. Upload files to VPS
echo "1. Uploading files to VPS..."
scp -r app/Http/Controllers/SchoolController.php root@your-vps-ip:/path/to/superadmin-backend/app/Http/Controllers/
scp -r resources/js/Pages/SuperAdmin/Schools.jsx root@your-vps-ip:/path/to/superadmin-backend/resources/js/Pages/SuperAdmin/
scp -r routes/web.php root@your-vps-ip:/path/to/superadmin-backend/routes/
scp -r app/Models/School.php root@your-vps-ip:/path/to/superadmin-backend/app/Models/
scp -r fix_vps_schools_database.php root@your-vps-ip:/path/to/superadmin-backend/

# 2. Run database fix on VPS
echo "2. Running database fix on VPS..."
ssh root@your-vps-ip "cd /path/to/superadmin-backend && php fix_vps_schools_database.php"

# 3. Clear cache and rebuild assets
echo "3. Clearing cache and rebuilding assets..."
ssh root@your-vps-ip "cd /path/to/superadmin-backend && php artisan config:clear && php artisan cache:clear && php artisan view:clear"

# 4. Rebuild frontend assets
echo "4. Rebuilding frontend assets..."
ssh root@your-vps-ip "cd /path/to/superadmin-backend && npm run build"

# 5. Restart services
echo "5. Restarting services..."
ssh root@your-vps-ip "systemctl restart apache2 && systemctl restart php8.1-fpm"

echo "✅ Schools fix deployed successfully!"
echo "The schools page should now work properly on VPS production."
