@echo off
title Reset E-Sign Database
cd /d "%~dp0..\.."
echo.
echo ============================================
echo  Reset E-Sign Database
echo ============================================
echo.
echo WARNING: This will delete all data and reset the database!
echo.
set /p confirm="Are you sure? (Y/N): "
if /i "%confirm%"=="Y" (
    echo.
    echo Resetting database...
    php artisan migrate:fresh --seed --force
    echo.
    echo Database reset complete!
    echo Default admin: admin@esign.local / password
) else (
    echo.
    echo Operation cancelled.
)
echo.
pause
