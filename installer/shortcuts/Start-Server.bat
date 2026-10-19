@echo off
title E-Sign Server
cd /d "%~dp0..\.."
echo.
echo ============================================
echo  Starting E-Sign Server
echo ============================================
echo.
echo Server will be available at: http://localhost:8000
echo Press Ctrl+C to stop the server
echo.
php artisan serve
pause
