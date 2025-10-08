#!/bin/bash

echo "🚀 Deploying to production server..."

# Production server details
PROD_SERVER="103.23.198.101"
PROD_PORT="8080"
PROD_USER="root"  # Adjust if different
PROD_PATH="/var/www/html/superadmin-campusway"  # Adjust path as needed

echo "📦 Building assets locally..."
npm run build

echo "📁 Checking local assets..."
echo "CSS files:"
ls -la public/build/assets/app-*.css
echo "JS files:"
ls -la public/build/assets/app-*.js

echo ""
echo "🔧 Uploading files to production server..."

# Upload build directory
echo "Uploading build assets..."
scp -P $PROD_PORT -r public/build/* $PROD_USER@$PROD_SERVER:$PROD_PATH/public/build/

# Upload any other necessary files
echo "Uploading updated source files..."
scp -P $PROD_PORT resources/js/Pages/SuperAdmin/MajorRecommendations.jsx $PROD_USER@$PROD_SERVER:$PROD_PATH/resources/js/Pages/SuperAdmin/
scp -P $PROD_PORT app/Http/Controllers/MajorRecommendationController.php $PROD_USER@$PROD_SERVER:$PROD_PATH/app/Http/Controllers/

echo "🔄 Running commands on production server..."
ssh -p $PROD_PORT $PROD_USER@$PROD_SERVER "cd $PROD_PATH && php artisan config:clear && php artisan cache:clear && php artisan view:clear"

echo ""
echo "✅ Deployment completed!"
echo ""
echo "🌐 Production URL: http://$PROD_SERVER:$PROD_PORT"
echo ""
echo "📋 If you still see 404 errors:"
echo "1. Hard refresh browser (Ctrl+F5)"
echo "2. Clear browser cache"
echo "3. Check if web server is serving files from public/build correctly"
echo ""
echo "🔍 To verify files on production:"
echo "ssh -p $PROD_PORT $PROD_USER@$PROD_SERVER 'ls -la $PROD_PATH/public/build/assets/app-*'"
