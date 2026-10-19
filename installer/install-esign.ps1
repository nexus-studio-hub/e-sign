# E-Sign System Installer for Windows
# PowerShell Script - Automated Installation

# Set error action preference
$ErrorActionPreference = "Stop"

# Configuration
$INSTALL_PATH = "C:\esign"
$DB_NAME = "esign"
$DB_USER = "postgres"
$DB_PASSWORD = "postgres"
$ADMIN_EMAIL = "admin@esign.local"
$ADMIN_PASSWORD = "password"

# Colors for output
function Write-ColorOutput($ForegroundColor) {
    $fc = $host.UI.RawUI.ForegroundColor
    $host.UI.RawUI.ForegroundColor = $ForegroundColor
    if ($args) {
        Write-Output $args
    }
    $host.UI.RawUI.ForegroundColor = $fc
}

function Write-Success($message) {
    Write-ColorOutput Green "[✓] $message"
}

function Write-Info($message) {
    Write-ColorOutput Cyan "[i] $message"
}

function Write-Error($message) {
    Write-ColorOutput Red "[✗] $message"
}

function Write-Header($message) {
    Write-Host ""
    Write-ColorOutput Yellow "═══════════════════════════════════════════════════════"
    Write-ColorOutput Yellow " $message"
    Write-ColorOutput Yellow "═══════════════════════════════════════════════════════"
    Write-Host ""
}

# Check if running as administrator
function Test-Administrator {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

# Check internet connection
function Test-InternetConnection {
    try {
        $null = Test-Connection -ComputerName google.com -Count 1 -Quiet
        return $true
    } catch {
        return $false
    }
}

# Check available disk space (in GB)
function Get-FreeDiskSpace {
    $drive = Get-PSDrive C
    return [math]::Round($drive.Free / 1GB, 2)
}

# Check if port is available
function Test-Port($port) {
    $connection = Get-NetTCPConnection -LocalPort $port -ErrorAction SilentlyContinue
    return $null -eq $connection
}

# Main installation function
function Install-ESign {
    Write-Header "E-SIGN SYSTEM INSTALLER v1.0"
    
    # Pre-installation checks
    Write-Info "Running pre-installation checks..."
    
    if (-not (Test-Administrator)) {
        Write-Error "This script must be run as Administrator!"
        exit 1
    }
    Write-Success "Running as Administrator"
    
    if (-not (Test-InternetConnection)) {
        Write-Error "No internet connection detected!"
        Write-Info "Internet connection is required to download dependencies."
        exit 1
    }
    Write-Success "Internet connection: OK"
    
    $freeSpace = Get-FreeDiskSpace
    if ($freeSpace -lt 5) {
        Write-Error "Insufficient disk space! Need at least 5GB, available: ${freeSpace}GB"
        exit 1
    }
    Write-Success "Disk space: ${freeSpace}GB available"
    
    # Check ports
    $ports = @(8000, 5432, 5173)
    foreach ($port in $ports) {
        if (Test-Port $port) {
            Write-Success "Port $port: Available"
        } else {
            Write-ColorOutput Yellow "[!] Port $port is in use. Installation will continue but may fail."
        }
    }
    
    Write-Host ""
    Write-Info "Installation will:"
    Write-Host "  - Install PHP 8.3, Composer, Node.js 18, PostgreSQL 16"
    Write-Host "  - Create database '$DB_NAME'"
    Write-Host "  - Install Laravel dependencies"
    Write-Host "  - Setup frontend assets"
    Write-Host "  - Create desktop shortcuts"
    Write-Host ""
    Write-Info "Estimated time: 10-15 minutes"
    Write-Host ""
    
    $confirm = Read-Host "Continue with installation? (Y/N)"
    if ($confirm -ne "Y" -and $confirm -ne "y") {
        Write-Info "Installation cancelled by user."
        exit 0
    }
    
    # Start installation
    Write-Header "STARTING INSTALLATION"
    
    # Install Chocolatey
    Write-Info "[1/10] Installing Chocolatey Package Manager..."
    try {
        if (-not (Get-Command choco -ErrorAction SilentlyContinue)) {
            Set-ExecutionPolicy Bypass -Scope Process -Force
            [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072
            Invoke-Expression ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
            $env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")
        }
        Write-Success "Chocolatey installed"
    } catch {
        Write-Error "Failed to install Chocolatey: $_"
        exit 1
    }
    
    # Install PHP
    Write-Info "[2/10] Installing PHP 8.3..."
    try {
        if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
            choco install php --version=8.3.0 -y --force
            $env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")
        }
        $phpVersion = php -v
        Write-Success "PHP installed: $($phpVersion[0])"
    } catch {
        Write-Error "Failed to install PHP: $_"
        exit 1
    }
    
    # Install Composer
    Write-Info "[3/10] Installing Composer..."
    try {
        if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
            choco install composer -y --force
            $env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")
        }
        $composerVersion = composer --version
        Write-Success "Composer installed: $composerVersion"
    } catch {
        Write-Error "Failed to install Composer: $_"
        exit 1
    }
    
    # Install Node.js
    Write-Info "[4/10] Installing Node.js 18 LTS..."
    try {
        if (-not (Get-Command node -ErrorAction SilentlyContinue)) {
            choco install nodejs-lts --version=18.20.0 -y --force
            $env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")
        }
        $nodeVersion = node -v
        $npmVersion = npm -v
        Write-Success "Node.js installed: $nodeVersion"
        Write-Success "npm installed: v$npmVersion"
    } catch {
        Write-Error "Failed to install Node.js: $_"
        exit 1
    }
    
    # Install PostgreSQL
    Write-Info "[5/10] Installing PostgreSQL 16..."
    try {
        if (-not (Get-Command psql -ErrorAction SilentlyContinue)) {
            choco install postgresql16 --params "/Password:$DB_PASSWORD" -y --force
            $env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")
            Start-Sleep -Seconds 10
        }
        Write-Success "PostgreSQL 16 installed"
    } catch {
        Write-Error "Failed to install PostgreSQL: $_"
        exit 1
    }
    
    # Configure PHP
    Write-Info "[6/10] Configuring PHP..."
    try {
        $phpIniPath = (php --ini | Select-String "Loaded Configuration File" | ForEach-Object { $_.ToString().Split(":")[1].Trim() })
        if ($phpIniPath -and (Test-Path $phpIniPath)) {
            $phpIni = Get-Content $phpIniPath
            $extensions = @("pdo_pgsql", "pgsql", "mbstring", "openssl", "fileinfo", "gd", "zip")
            foreach ($ext in $extensions) {
                if ($phpIni -notmatch "extension=$ext") {
                    Add-Content $phpIniPath "extension=$ext"
                }
            }
            Write-Success "PHP extensions enabled"
        }
    } catch {
        Write-ColorOutput Yellow "[!] Could not configure PHP automatically. May need manual configuration."
    }
    
    # Setup PostgreSQL Database
    Write-Info "[7/10] Setting up PostgreSQL database..."
    try {
        Start-Sleep -Seconds 5
        $env:PGPASSWORD = $DB_PASSWORD
        & "C:\Program Files\PostgreSQL\16\bin\psql.exe" -U $DB_USER -c "CREATE DATABASE $DB_NAME;" 2>$null
        Write-Success "Database '$DB_NAME' created"
    } catch {
        Write-ColorOutput Yellow "[!] Database may already exist or need manual creation"
    }
    
    # Get current directory (project root)
    $projectRoot = Split-Path -Parent $PSScriptRoot
    
    # Configure Laravel
    Write-Info "[8/10] Configuring Laravel..."
    try {
        Set-Location $projectRoot
        
        if (-not (Test-Path ".env")) {
            Copy-Item ".env.example" ".env"
        }
        
        # Update .env file
        $envContent = Get-Content ".env"
        $envContent = $envContent -replace "DB_CONNECTION=.*", "DB_CONNECTION=pgsql"
        $envContent = $envContent -replace "DB_HOST=.*", "DB_HOST=127.0.0.1"
        $envContent = $envContent -replace "DB_PORT=.*", "DB_PORT=5432"
        $envContent = $envContent -replace "DB_DATABASE=.*", "DB_DATABASE=$DB_NAME"
        $envContent = $envContent -replace "DB_USERNAME=.*", "DB_USERNAME=$DB_USER"
        $envContent = $envContent -replace "DB_PASSWORD=.*", "DB_PASSWORD=$DB_PASSWORD"
        Set-Content ".env" $envContent
        
        Write-Success ".env configured"
        
        Write-Info "Installing Composer dependencies (this may take 2-3 minutes)..."
        composer install --no-interaction --prefer-dist --optimize-autoloader
        Write-Success "Composer dependencies installed"
        
        php artisan key:generate --force
        Write-Success "Application key generated"
        
        php artisan storage:link --force
        Write-Success "Storage linked"
        
    } catch {
        Write-Error "Failed to configure Laravel: $_"
        exit 1
    }
    
    # Run migrations and seeders
    Write-Info "[9/10] Setting up database..."
    try {
        php artisan migrate:fresh --seed --force
        Write-Success "Database migrated and seeded"
    } catch {
        Write-Error "Failed to setup database: $_"
        exit 1
    }
    
    # Install frontend dependencies and build
    Write-Info "[10/10] Installing frontend dependencies and building assets..."
    try {
        Write-Info "Installing npm packages (this may take 3-5 minutes)..."
        npm install --legacy-peer-deps
        Write-Success "npm packages installed"
        
        Write-Info "Building frontend assets..."
        npm run build
        Write-Success "Frontend assets built"
    } catch {
        Write-Error "Failed to setup frontend: $_"
        exit 1
    }
    
    # Create shortcuts
    Write-Info "Creating desktop shortcuts..."
    try {
        $WshShell = New-Object -ComObject WScript.Shell
        $Desktop = [System.Environment]::GetFolderPath('Desktop')
        
        # Start Server shortcut
        $Shortcut = $WshShell.CreateShortcut("$Desktop\Start E-Sign Server.lnk")
        $Shortcut.TargetPath = "$projectRoot\installer\shortcuts\Start-Server.bat"
        $Shortcut.WorkingDirectory = $projectRoot
        $Shortcut.Save()
        
        # Start Dev shortcut
        $Shortcut = $WshShell.CreateShortcut("$Desktop\Start E-Sign Dev.lnk")
        $Shortcut.TargetPath = "$projectRoot\installer\shortcuts\Start-Dev.bat"
        $Shortcut.WorkingDirectory = $projectRoot
        $Shortcut.Save()
        
        # Stop All shortcut
        $Shortcut = $WshShell.CreateShortcut("$Desktop\Stop E-Sign.lnk")
        $Shortcut.TargetPath = "$projectRoot\installer\shortcuts\Stop-All.bat"
        $Shortcut.WorkingDirectory = $projectRoot
        $Shortcut.Save()
        
        Write-Success "Desktop shortcuts created"
    } catch {
        Write-ColorOutput Yellow "[!] Could not create shortcuts: $_"
    }
    
    # Configure Windows Firewall
    Write-Host ""
    $firewallConfirm = Read-Host "Configure Windows Firewall to allow E-Sign ports? (Y/N)"
    if ($firewallConfirm -eq "Y" -or $firewallConfirm -eq "y") {
        try {
            New-NetFirewallRule -DisplayName "E-Sign Laravel Server" -Direction Inbound -LocalPort 8000 -Protocol TCP -Action Allow -ErrorAction SilentlyContinue
            New-NetFirewallRule -DisplayName "E-Sign PostgreSQL" -Direction Inbound -LocalPort 5432 -Protocol TCP -Action Allow -ErrorAction SilentlyContinue
            New-NetFirewallRule -DisplayName "E-Sign Vite Dev Server" -Direction Inbound -LocalPort 5173 -Protocol TCP -Action Allow -ErrorAction SilentlyContinue
            Write-Success "Firewall rules added"
        } catch {
            Write-ColorOutput Yellow "[!] Could not configure firewall automatically"
        }
    }
    
    # Installation complete
    Write-Header "INSTALLATION COMPLETE!"
    
    Write-Host ""
    Write-ColorOutput Green "E-Sign System has been successfully installed!"
    Write-Host ""
    Write-Info "Installation Details:"
    Write-Host "  Location:        $projectRoot"
    Write-Host "  Database:        $DB_NAME (PostgreSQL 16)"
    Write-Host "  Default Admin:   $ADMIN_EMAIL"
    Write-Host "  Default Password: $ADMIN_PASSWORD"
    Write-Host ""
    Write-Info "Desktop Shortcuts Created:"
    Write-Host "  • Start E-Sign Server  - Run Laravel server"
    Write-Host "  • Start E-Sign Dev     - Run development mode"
    Write-Host "  • Stop E-Sign          - Stop all services"
    Write-Host ""
    Write-Info "Next Steps:"
    Write-Host "  1. Double-click 'Start E-Sign Server' on desktop"
    Write-Host "  2. Open browser to: http://localhost:8000"
    Write-Host "  3. Login with: $ADMIN_EMAIL / $ADMIN_PASSWORD"
    Write-Host "  4. Change password after first login"
    Write-Host ""
    
    $openBrowser = Read-Host "Open browser now? (Y/N)"
    if ($openBrowser -eq "Y" -or $openBrowser -eq "y") {
        Start-Process "http://localhost:8000"
    }
}

# Run installation
try {
    Install-ESign
} catch {
    Write-Error "Installation failed: $_"
    Write-Host ""
    Write-Info "Please check the error message above and try again."
    Write-Info "For support, please contact your system administrator."
    exit 1
}
