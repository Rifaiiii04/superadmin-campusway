#!/bin/bash

# Deploy JSON parsing fix and student choice status fix to VPS
# This script fixes the issues where frontend receives HTML instead of JSON
# and student choice status not displaying correctly in superadmin

echo "🚀 Deploying JSON parsing fix and student choice status fix..."

# VPS Configuration
VPS_USER="marketing"
VPS_IP="103.23.198.101"
PROJECT_PATH="/var/www/superadmin/superadmin-campusway"

# Files to update
FILES=(
    "app/Http/Controllers/SchoolDashboardController.php"
    "app/Http/Controllers/SchoolController.php"
    "app/Http/Kernel.php"
    "routes/api.php"
    "resources/js/Pages/SuperAdmin/SchoolDetail.jsx"
)

echo "📁 Uploading updated files..."

for file in "${FILES[@]}"; do
    echo "  📤 Uploading $file..."
    scp "$file" $VPS_USER@$VPS_IP:$PROJECT_PATH/$file
    if [ $? -eq 0 ]; then
        echo "    ✅ $file uploaded successfully"
    else
        echo "    ❌ Failed to upload $file"
        exit 1
    fi
done

echo "🔧 Setting permissions..."
ssh $VPS_USER@$VPS_IP "cd $PROJECT_PATH && sudo chown -R www-data:www-data . && sudo chmod -R 755 ."

echo "🔄 Clearing caches..."
ssh $VPS_USER@$VPS_IP "cd $PROJECT_PATH && php artisan config:clear && php artisan route:clear && php artisan view:clear"

echo "🧪 Testing API endpoints..."
echo "  Testing school login endpoint..."
ssh $VPS_USER@$VPS_IP "curl -s -X POST http://localhost/api/school/login -H 'Content-Type: application/json' -d '{\"npsn\":\"12345678\",\"password\":\"test123\"}' | head -c 200"

echo ""
echo "  Testing students endpoint (should return JSON, not HTML)..."
ssh $VPS_USER@$VPS_IP "curl -s -H 'Accept: application/json' http://localhost/api/school/students | head -c 200"

echo ""
echo "✅ Deployment completed!"
echo ""
echo "🔍 Manual verification steps:"
echo "1. Test school login at: http://103.23.198.101/super-admin/api/school/login"
echo "2. Test students endpoint with valid token"
echo "3. Check superadmin school detail page for correct student choice status"
echo ""
echo "📋 Changes made:"
echo "- Fixed API authentication to use school.auth middleware instead of auth:sanctum"
echo "- Fixed SchoolDashboardController to get school from middleware instead of request parameter"
echo "- Fixed SchoolController to load majorRecommendation relationship"
echo "- Fixed frontend to use correct camelCase property names"
echo "- Registered school.auth middleware in Kernel.php"
