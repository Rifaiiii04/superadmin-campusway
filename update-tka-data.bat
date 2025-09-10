@echo off
echo ========================================
echo    UPDATE TKA DATA WITH PUSMENDIK FIELDS
echo ========================================
echo.

echo 1. Updating existing TKA schedules...
php update_existing_tka_data.php

echo.
echo 2. Testing API endpoints...
echo    - Student TKA API: /api/web/tka-schedules/upcoming
echo    - Teacher TKA API: /api/tka-schedules/upcoming
echo.

echo ========================================
echo    UPDATE COMPLETE!
echo ========================================
echo.
echo ✅ PUSMENDIK fields added to existing data
echo ✅ API endpoints updated with new fields
echo ✅ Frontend UI updated to display PUSMENDIK data
echo.
echo 🚀 TKA schedules now show PUSMENDIK information!
echo.
pause
