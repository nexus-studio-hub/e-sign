@echo off
title Stop E-Sign Services
echo.
echo ============================================
echo  Stopping E-Sign Services
echo ============================================
echo.
echo Stopping all E-Sign services...
taskkill /F /IM php.exe /T 2>nul
taskkill /F /IM node.exe /T 2>nul
echo.
echo All services stopped.
echo.
pause
