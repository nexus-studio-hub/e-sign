@echo off
title Update E-Sign System
cd /d "%~dp0"

echo.
echo ============================================
echo  Update E-Sign System
echo ============================================
echo.
echo This will update E-Sign to the latest version.
echo.
set /p confirm="Continue? (Y/N): "
if /i not "%confirm%"=="Y" (
    echo.
    echo Update cancelled.
    pause
    exit /b 0
)

echo.
echo [1/5] Pulling latest code...
git pull origin main
if errorlevel 1 (
    echo.
    echo Error: Failed to pull latest code
    echo Make sure Git is installed and you have internet connection.
    pause
    exit /b 1
)

echo.
echo [2/5] Updating Composer dependencies...
composer install --no-dev --optimize-autoloader
if errorlevel 1 (
    echo.
    echo Error: Failed to update Composer dependencies
    pause
    exit /b 1
)

echo.
echo [3/5] Updating npm dependencies...
npm install --legacy-peer-deps
if errorlevel 1 (
    echo.
    echo Error: Failed to update npm dependencies
    pause
    exit /b 1
)

echo.
echo [4/5] Running database migrations...
php artisan migrate --force
if errorlevel 1 (
    echo.
    echo Error: Failed to run migrations
    pause
    exit /b 1
)

echo.
echo [5/5] Building frontend assets...
npm run build
if errorlevel 1 (
    echo.
    echo Error: Failed to build assets
    pause
    exit /b 1
)

echo.
echo ============================================
echo  UPDATE COMPLETE!
echo ============================================
echo.
echo E-Sign System has been updated successfully.
echo Please restart the server if it's running.
echo.
pause
