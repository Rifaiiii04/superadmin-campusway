#!/bin/bash

# =====================================================
# Deploy API Fixes to Production VPS
# =====================================================

echo "=========================================="
echo "Deploying API Fixes to Production"
echo "=========================================="

# VPS Configuration
VPS_USER="root"
VPS_IP="103.23.198.101"
VPS_PATH="/var/www/html/super-admin"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo ""
echo -e "${YELLOW}Step 1: Uploading API Routes Configuration...${NC}"
scp routes/api.php $VPS_USER@$VPS_IP:$VPS_PATH/routes/

echo ""
echo -e "${YELLOW}Step 2: Uploading TkaScheduleController...${NC}"
scp app/Http/Controllers/TkaScheduleController.php $VPS_USER@$VPS_IP:$VPS_PATH/app/Http/Controllers/

echo ""
echo -e "${YELLOW}Step 3: Uploading CORS Configuration...${NC}"
scp config/cors.php $VPS_USER@$VPS_IP:$VPS_PATH/config/

echo ""
echo -e "${YELLOW}Step 4: Uploading API Documentation...${NC}"
scp API_ENDPOINTS_PRODUCTION.md $VPS_USER@$VPS_IP:$VPS_PATH/

echo ""
echo -e "${YELLOW}Step 5: Clearing Laravel Cache on VPS...${NC}"
ssh $VPS_USER@$VPS_IP << 'ENDSSH'
cd /var/www/html/super-admin
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
ENDSSH

echo ""
echo -e "${YELLOW}Step 6: Setting Permissions...${NC}"
ssh $VPS_USER@$VPS_IP << 'ENDSSH'
cd /var/www/html/super-admin
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
ENDSSH

echo ""
echo -e "${YELLOW}Step 7: Restarting Apache...${NC}"
ssh $VPS_USER@$VPS_IP "systemctl restart apache2"

echo ""
echo -e "${GREEN}=========================================="
echo -e "✅ Deployment Complete!"
echo -e "==========================================${NC}"
echo ""
echo "Testing Endpoints:"
echo "- Health Check: http://103.23.198.101/super-admin/api/web/health"
echo "- Schools: http://103.23.198.101/super-admin/api/web/schools"
echo "- Majors: http://103.23.198.101/super-admin/api/web/majors"
echo ""
echo "Next Steps:"
echo "1. Test API endpoints dengan browser atau curl"
echo "2. Update frontend environment variables"
echo "3. Test frontend integration dengan backend"
echo ""
echo -e "${YELLOW}Run these tests:${NC}"
echo "curl http://103.23.198.101/super-admin/api/web/health"
echo "curl http://103.23.198.101/super-admin/api/web/schools"
echo "curl http://103.23.198.101/super-admin/api/web/majors"
echo ""

