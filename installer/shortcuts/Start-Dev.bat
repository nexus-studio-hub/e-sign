@echo off
title E-Sign Development Mode
cd /d "%~dp0..\.."
echo.
echo ============================================
echo  Starting E-Sign in Development Mode
echo ============================================
echo.
echo Vite dev server will be available at: http://localhost:5173
echo Laravel server at: http://localhost:8000
echo.
echo Press Ctrl+C to stop
echo.
start "Laravel Server" cmd /k "php artisan serve"
timeout /t 3 /nobreak >nul
npm run dev
pause
