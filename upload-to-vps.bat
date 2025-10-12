@echo off
REM TKA SuperAdmin - Upload to VPS Script
REM Run this script from Windows to upload files to VPS

echo 🚀 TKA SuperAdmin - Uploading to VPS
echo ====================================
echo.

REM Set variables
set VPS_USER=marketing
set VPS_HOST=103.23.198.101
set VPS_SUPERADMIN_DIR=/var/www/superadmin
set LOCAL_SUPERADMIN_DIR=.

echo 📁 Uploading SuperAdmin Laravel Backend...
echo    Source: %LOCAL_SUPERADMIN_DIR%
echo    Destination: %VPS_USER%@%VPS_HOST%:%VPS_SUPERADMIN_DIR%
echo.

REM Upload SuperAdmin files
scp -r %LOCAL_SUPERADMIN_DIR% %VPS_USER%@%VPS_HOST%:%VPS_SUPERADMIN_DIR%/

if %ERRORLEVEL% EQU 0 (
    echo ✅ SuperAdmin files uploaded successfully
) else (
    echo ❌ Failed to upload SuperAdmin files
    pause
    exit /b 1
)

echo.
echo 📁 Uploading deployment script...
scp deploy-to-vps.sh %VPS_USER%@%VPS_HOST%:%VPS_SUPERADMIN_DIR%/

if %ERRORLEVEL% EQU 0 (
    echo ✅ Deployment script uploaded successfully
) else (
    echo ❌ Failed to upload deployment script
    pause
    exit /b 1
)

echo.
echo 🎉 Upload completed successfully!
echo ================================
echo.
echo Next steps:
echo 1. SSH to VPS: ssh %VPS_USER%@%VPS_HOST%
echo 2. Run deployment: cd %VPS_SUPERADMIN_DIR% && chmod +x deploy-to-vps.sh && ./deploy-to-vps.sh
echo 3. Test the application
echo.
pause
