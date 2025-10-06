#!/bin/bash

echo "🚀 PUSH TO GITHUB - SUPERADMIN UI FIX"
echo "====================================="

cd /var/www/superadmin/superadmin-campusway

echo ""
echo "=== STEP 1: CHECK GIT STATUS ==="
git status

echo ""
echo "=== STEP 2: ADD ALL CHANGES ==="
git add .

echo ""
echo "=== STEP 3: COMMIT CHANGES ==="
git commit -m "🚀 Fix SuperAdmin UI - React + Vite + Laravel

✅ Fixed routes to render UI instead of JSON
✅ Updated app.blade.php for proper Vite integration
✅ Fixed double URL redirect issues
✅ Added proper Inertia.js setup
✅ Ready for production deployment

Changes:
- routes/web.php: Updated to render Inertia components
- resources/views/app.blade.php: Fixed Vite asset loading
- deploy-ui-fix.sh: Added deployment script
- All UI components ready for production"

echo ""
echo "=== STEP 4: PUSH TO GITHUB ==="
git push origin main

echo ""
echo "🎉 PUSH TO GITHUB COMPLETE!"
echo "==========================="
echo "✅ All changes committed"
echo "✅ Pushed to GitHub"
echo ""
echo "📋 Next steps on VPS:"
echo "1. git pull origin main"
echo "2. chmod +x deploy-ui-fix.sh"
echo "3. ./deploy-ui-fix.sh"
echo ""
echo "🚀 Ready for production deployment!"
