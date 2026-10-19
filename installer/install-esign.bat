@echo off
:: E-Sign System Installer Launcher
:: This script launches the PowerShell installer with administrator privileges

title E-Sign System Installer

:: Check for administrator privileges
net session >nul 2>&1
if %errorLevel% == 0 (
    goto :run_installer
) else (
    echo.
    echo ============================================
    echo  E-Sign System Installer
    echo ============================================
    echo.
    echo This installer requires administrator privileges.
    echo Please right-click this file and select "Run as administrator"
    echo.
    echo Or press any key to attempt auto-elevation...
    pause >nul
    
    :: Attempt to elevate
    powershell -Command "Start-Process '%~f0' -Verb RunAs"
    exit /b
)

:run_installer
cd /d "%~dp0"
echo.
echo ============================================
echo  E-Sign System Installer
echo ============================================
echo.
echo Starting installation...
echo.

:: Run PowerShell installer
powershell -ExecutionPolicy Bypass -File "%~dp0install-esign.ps1"

if %errorLevel% == 0 (
    echo.
    echo ============================================
    echo  Installation completed successfully!
    echo ============================================
    echo.
) else (
    echo.
    echo ============================================
    echo  Installation failed!
    echo ============================================
    echo.
    echo Please check the log file for details.
    echo.
)

pause
