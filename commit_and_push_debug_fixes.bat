@echo off
echo === COMMITTING AND PUSHING DEBUG FIXES TO GITHUB ===

echo 1. Adding all files...
git add .

echo 2. Committing changes...
git commit -m "Add comprehensive debugging to all SuperAdmin controllers

- Added detailed logging to all controller index methods
- Added debug data to Inertia responses for frontend debugging
- Added error handling with stack traces
- Added console.log debugging to frontend components
- Improved pagination debugging
- Added database connection testing

Controllers updated:
- SchoolController
- MajorRecommendationController  
- StudentController
- QuestionController
- ResultController
- TkaScheduleController

Frontend components updated:
- Schools.jsx
- Students.jsx
- MajorRecommendations.jsx (already had debug)
- TkaSchedules.jsx (already had debug)"

echo 3. Pushing to GitHub...
git push origin main

echo ✅ Debug fixes committed and pushed successfully!
echo.
echo Next steps:
echo 1. Go to VPS and run: git pull origin main
echo 2. Run: npm run build
echo 3. Run: php artisan config:cache
echo 4. Run: php artisan route:cache
echo 5. Run: sudo systemctl restart apache2
echo 6. Check browser console for debug logs
echo 7. Check Laravel logs: tail -f storage/logs/laravel.log
pause
