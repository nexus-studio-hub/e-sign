# E-Sign System - Installation Guide

Complete installation guide for E-Sign System on Windows.

## Table of Contents

- [System Requirements](#system-requirements)
- [Automated Installation (Recommended)](#automated-installation-recommended)
- [Manual Installation](#manual-installation)
- [Post-Installation Setup](#post-installation-setup)
- [Troubleshooting](#troubleshooting)
- [Uninstallation](#uninstallation)

---

## System Requirements

### Minimum Requirements
- **OS:** Windows 10 (64-bit) or Windows 11
- **RAM:** 8GB
- **Storage:** 5GB free disk space
- **Processor:** Dual-core 2.0 GHz
- **Internet:** Required for installation
- **Access:** Administrator privileges

### Recommended Requirements
- **OS:** Windows 11 (64-bit)
- **RAM:** 16GB
- **Storage:** 10GB free disk space (SSD preferred)
- **Processor:** Quad-core 2.5 GHz or better
- **Internet:** Broadband connection

### Software Dependencies (Auto-installed)
- PHP 8.3
- Composer 2.7+
- Node.js 18 LTS
- npm 10+
- PostgreSQL 16
- Git (optional, for updates)

---

## Automated Installation (Recommended)

The automated installer will install all dependencies and configure the system automatically.

### Step 1: Download or Clone Repository

**Option A: Using Git**
```bash
git clone <repository-url> C:\esign
cd C:\esign
```

**Option B: Download ZIP**
1. Download ZIP from repository
2. Extract to `C:\esign\`
3. Navigate to extracted folder

### Step 2: Run Installer

1. Open `C:\esign\installer\` folder
2. Right-click `install-esign.bat`
3. Select **"Run as administrator"**
4. Click **"Yes"** on UAC prompt

### Step 3: Follow Installation Wizard

The installer will:
1. Check system requirements
2. Verify administrator privileges
3. Test internet connection
4. Check available disk space
5. Verify ports availability (8000, 5432, 5173)

You'll see:
```
╔════════════════════════════════════════════════════════╗
║     E-SIGN SYSTEM INSTALLER v1.0                       ║
╚════════════════════════════════════════════════════════╝

[✓] Windows 11 detected
[✓] Running as Administrator
[✓] Internet connection: OK
[✓] Disk space: 50GB available
[✓] Port 8000: Available
[✓] Port 5432: Available
[✓] Port 5173: Available

Installation will:
- Install PHP 8.3, Composer, Node.js 18, PostgreSQL 16
- Create database 'esign'
- Install Laravel dependencies
- Setup frontend assets
- Create desktop shortcuts

Estimated time: 10-15 minutes

Continue? (Y/N):
```

6. Type **Y** and press Enter

### Step 4: Wait for Installation

The installer will automatically:

**[1/10] Install Chocolatey** (~1 min)
- Windows package manager

**[2/10] Install PHP 8.3** (~2 min)
- PHP runtime and extensions

**[3/10] Install Composer** (~1 min)
- PHP dependency manager

**[4/10] Install Node.js 18** (~2 min)
- JavaScript runtime and npm

**[5/10] Install PostgreSQL 16** (~2 min)
- Database server

**[6/10] Configure PHP** (~1 min)
- Enable required extensions
- Set memory limits

**[7/10] Setup Database** (~1 min)
- Create 'esign' database
- Configure connection

**[8/10] Configure Laravel** (~2 min)
- Install Composer dependencies
- Generate application key
- Link storage

**[9/10] Setup Database Schema** (~1 min)
- Run migrations
- Seed initial data

**[10/10] Setup Frontend** (~3-5 min)
- Install npm packages
- Build assets

**Total Time:** 10-15 minutes (depending on internet speed)

### Step 5: Configure Firewall (Optional)

When prompted:
```
Configure Windows Firewall to allow E-Sign ports? (Y/N):
```

- Type **Y** to automatically configure firewall
- Type **N** to skip (you'll need to configure manually later)

### Step 6: Installation Complete

You'll see:
```
╔════════════════════════════════════════════════════════╗
║     INSTALLATION COMPLETE!                             ║
╚════════════════════════════════════════════════════════╝

E-Sign System has been successfully installed!

Installation Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Location:        C:\esign\
Database:        esign (PostgreSQL 16)
Default Admin:   admin@esign.local
Default Password: password

Desktop Shortcuts Created:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
• Start E-Sign Server  - Run Laravel server
• Start E-Sign Dev     - Run development mode
• Stop E-Sign          - Stop all services

Next Steps:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Double-click "Start E-Sign Server" on desktop
2. Open browser to: http://localhost:8000
3. Login with: admin@esign.local / password
4. Change password after first login

Open browser now? (Y/N):
```

7. Type **Y** to open browser automatically

---

## Manual Installation

For advanced users who prefer manual installation.

### Step 1: Install Chocolatey

Open PowerShell as Administrator:
```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force
[System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072
iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
```

### Step 2: Install Dependencies

```powershell
# Install PHP 8.3
choco install php --version=8.3.0 -y

# Install Composer
choco install composer -y

# Install Node.js 18
choco install nodejs-lts --version=18.20.0 -y

# Install PostgreSQL 16
choco install postgresql16 --params "/Password:postgres" -y

# Refresh environment variables
refreshenv
```

### Step 3: Configure PHP

1. Locate `php.ini`:
   ```bash
   php --ini
   ```

2. Edit `php.ini` and enable extensions:
   ```ini
   extension=pdo_pgsql
   extension=pgsql
   extension=mbstring
   extension=openssl
   extension=fileinfo
   extension=gd
   extension=zip
   
   memory_limit = 512M
   upload_max_filesize = 10M
   post_max_size = 10M
   ```

### Step 4: Setup PostgreSQL Database

```bash
# Set password environment variable
set PGPASSWORD=postgres

# Create database
psql -U postgres -c "CREATE DATABASE esign;"

# Verify connection
psql -U postgres -d esign -c "SELECT version();"
```

### Step 5: Clone Project

```bash
git clone <repository-url> C:\esign
cd C:\esign
```

### Step 6: Configure Laravel

```bash
# Copy environment file
copy .env.example .env

# Edit .env file and set:
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=esign
# DB_USERNAME=postgres
# DB_PASSWORD=postgres

# Install Composer dependencies
composer install

# Generate application key
php artisan key:generate

# Link storage
php artisan storage:link

# Run migrations and seeders
php artisan migrate:fresh --seed
```

### Step 7: Setup Frontend

```bash
# Install npm packages
npm install --legacy-peer-deps

# Build assets
npm run build
```

### Step 8: Configure Firewall

```powershell
# Allow Laravel server
New-NetFirewallRule -DisplayName "E-Sign Laravel" -Direction Inbound -LocalPort 8000 -Protocol TCP -Action Allow

# Allow PostgreSQL
New-NetFirewallRule -DisplayName "E-Sign PostgreSQL" -Direction Inbound -LocalPort 5432 -Protocol TCP -Action Allow

# Allow Vite dev server
New-NetFirewallRule -DisplayName "E-Sign Vite" -Direction Inbound -LocalPort 5173 -Protocol TCP -Action Allow
```

### Step 9: Start Application

```bash
php artisan serve
```

Open browser to http://localhost:8000

---

## Post-Installation Setup

### 1. First Login

1. Open http://localhost:8000
2. Login with:
   - Email: `admin@esign.local`
   - Password: `password`

### 2. Change Default Password

1. Click on profile icon (top-right)
2. Select "Change Password"
3. Enter new secure password
4. Save changes

### 3. Configure Application Settings

1. Navigate to Settings
2. Update:
   - Company information
   - Email settings (if using email notifications)
   - System preferences

### 4. Create Users

1. Go to Admin → Users
2. Click "Add User"
3. Fill in user details
4. Assign appropriate role
5. Save

### 5. Setup Roles (Optional)

1. Go to Settings → Roles
2. Review default roles:
   - PIC Development (Level 1)
   - Group Leader (Level 2)
   - Manager (Level 3)
   - Director (Level 3)
3. Add custom roles if needed

### 6. Add Suppliers

1. Go to Admin → Suppliers
2. Click "Add Supplier"
3. Fill in supplier details
4. Save

---

## Troubleshooting

### Installation Fails

**Problem:** Installer stops with error

**Solutions:**
1. Check internet connection
2. Disable antivirus temporarily
3. Run as Administrator
4. Check installation log: `C:\esign\installer\install.log`
5. Try manual installation

### Port Already in Use

**Problem:** Port 8000, 5432, or 5173 is in use

**Solutions:**
```bash
# Check what's using the port
netstat -ano | findstr :8000

# Kill the process (replace PID with actual process ID)
taskkill /PID <PID> /F

# Or change port in .env:
# APP_URL=http://localhost:8001
# Then run: php artisan serve --port=8001
```

### PostgreSQL Service Won't Start

**Problem:** Database connection fails

**Solutions:**
1. Open Services (Win+R → `services.msc`)
2. Find "postgresql-x64-16"
3. Right-click → Start
4. Set Startup Type to "Automatic"

### Composer Install Fails

**Problem:** Composer dependencies fail to install

**Solutions:**
```bash
# Clear Composer cache
composer clear-cache

# Try again with verbose output
composer install -vvv

# If still fails, try without cache
composer install --no-cache
```

### npm Install Fails

**Problem:** npm packages fail to install

**Solutions:**
```bash
# Clear npm cache
npm cache clean --force

# Delete node_modules and package-lock.json
rmdir /s /q node_modules
del package-lock.json

# Try again
npm install --legacy-peer-deps
```

### Migration Fails

**Problem:** Database migration errors

**Solutions:**
```bash
# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# If connection OK, try fresh migration
php artisan migrate:fresh --seed --force

# Check PostgreSQL logs
# C:\Program Files\PostgreSQL\16\data\log\
```

### Permission Denied

**Problem:** Cannot write to storage or cache

**Solutions:**
1. Right-click project folder → Properties
2. Security tab → Edit
3. Add "Full Control" for your user
4. Apply to all subfolders

Or run as Administrator:
```bash
# In project root
icacls storage /grant Users:F /T
icacls bootstrap\cache /grant Users:F /T
```

### White Screen / 500 Error

**Problem:** Application shows blank page or 500 error

**Solutions:**
1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode in `.env`:
   ```
   APP_DEBUG=true
   ```
3. Clear all caches:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```
4. Regenerate autoload:
   ```bash
   composer dump-autoload
   ```

---

## Uninstallation

### Remove Application

1. Stop all E-Sign services (use "Stop E-Sign" shortcut)
2. Delete project folder: `C:\esign\`
3. Delete desktop shortcuts

### Remove Dependencies (Optional)

If you want to completely remove all installed software:

```powershell
# Uninstall via Chocolatey
choco uninstall php -y
choco uninstall composer -y
choco uninstall nodejs-lts -y
choco uninstall postgresql16 -y

# Remove Chocolatey itself (optional)
# Follow: https://docs.chocolatey.org/en-us/choco/uninstallation
```

### Remove Database

```bash
# Drop database
psql -U postgres -c "DROP DATABASE esign;"
```

### Remove Firewall Rules

```powershell
Remove-NetFirewallRule -DisplayName "E-Sign Laravel"
Remove-NetFirewallRule -DisplayName "E-Sign PostgreSQL"
Remove-NetFirewallRule -DisplayName "E-Sign Vite"
```

---

## Additional Resources

- [User Manual](installer/README.txt) - Complete user guide
- [README](README.md) - Project overview
- [Laravel Documentation](https://laravel.com/docs) - Laravel framework docs
- [Vue.js Documentation](https://vuejs.org) - Vue.js framework docs

---

## Support

For installation issues:
1. Check this guide thoroughly
2. Check `installer/README.txt`
3. Check installation log: `C:\esign\installer\install.log`
4. Open GitHub issue with error details
5. Contact system administrator

---

**Version:** 1.0  
**Last Updated:** 2026-10-19
