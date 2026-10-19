╔════════════════════════════════════════════════════════╗
║     E-SIGN SYSTEM - USER MANUAL                        ║
╚════════════════════════════════════════════════════════╝

QUICK START
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Double-click "Start E-Sign Server" on desktop
2. Open browser to: http://localhost:8000
3. Login with:
   Email: admin@esign.local
   Password: password
4. Change password after first login

INSTALLATION
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
If you haven't installed yet:
1. Navigate to the installer folder
2. Right-click "install-esign.bat"
3. Select "Run as administrator"
4. Follow on-screen instructions
5. Wait 10-15 minutes for installation to complete

DESKTOP SHORTCUTS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
• Start E-Sign Server
  Run this to start the application server.
  Server will be available at http://localhost:8000

• Start E-Sign Dev
  Run this for development mode with hot-reload.
  Use this when making changes to the code.

• Stop E-Sign
  Run this to stop all E-Sign services.

• Reset Database (Migrate-Fresh.bat in shortcuts folder)
  Run this to reset database to initial state.
  WARNING: This will delete all data!

DEVELOPMENT MODE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
For developers who want to modify the system:

1. Double-click "Start E-Sign Dev"
2. Edit files in: resources/js/ or resources/css/
3. Changes will auto-refresh in browser
4. Frontend dev server: http://localhost:5173
5. Backend API: http://localhost:8000

UPDATING THE SYSTEM
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Stop all E-Sign services (use "Stop E-Sign" shortcut)
2. Double-click "update-esign.bat" in project root
3. Wait for update to complete
4. Restart E-Sign Server

TROUBLESHOOTING
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Problem: Server won't start
Solution:
  - Check if port 8000 is already in use
  - Run "Stop E-Sign" first, then try again
  - Open Command Prompt and run: netstat -ano | findstr :8000
  - If port is in use, kill the process or use different port

Problem: Database connection error
Solution:
  - Check if PostgreSQL service is running
  - Open Services (Win+R, type: services.msc)
  - Find "postgresql-x64-16" and start it
  - Verify credentials in .env file

Problem: Frontend not loading
Solution:
  - Run "npm run build" in project root
  - Clear browser cache (Ctrl+Shift+Delete)
  - Hard refresh (Ctrl+F5)

Problem: Permission denied errors
Solution:
  - Right-click shortcut → Run as Administrator
  - Check folder permissions for project directory
  - Ensure storage/ and bootstrap/cache/ are writable

Problem: Composer/npm install fails
Solution:
  - Check internet connection
  - Clear Composer cache: composer clear-cache
  - Clear npm cache: npm cache clean --force
  - Try again with: composer install --no-cache

Problem: Migration fails
Solution:
  - Check database connection in .env
  - Verify PostgreSQL is running
  - Try: php artisan migrate:fresh --seed --force
  - Check database user has proper permissions

SYSTEM REQUIREMENTS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
• Windows 10/11 (64-bit)
• 8GB RAM minimum (16GB recommended)
• 5GB free disk space
• Internet connection (for installation)
• Administrator access

INSTALLED COMPONENTS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
• PHP 8.3
• Composer 2.7+
• Node.js 18 LTS
• npm 10+
• PostgreSQL 16
• Git (optional, for updates)

DEFAULT CREDENTIALS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Superadmin:
  Email: admin@esign.local
  Password: password

Database:
  Host: 127.0.0.1
  Port: 5432
  Database: esign
  Username: postgres
  Password: postgres

IMPORTANT SECURITY NOTES
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1. Change default admin password immediately after first login
2. Change database password in production environment
3. Set APP_DEBUG=false in .env for production
4. Set APP_ENV=production in .env for production
5. Use strong passwords for all accounts
6. Keep system updated regularly

USEFUL COMMANDS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Start server:
  php artisan serve

Run migrations:
  php artisan migrate

Reset database:
  php artisan migrate:fresh --seed

Clear cache:
  php artisan cache:clear
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear

Build frontend:
  npm run build

Development mode:
  npm run dev

Check Laravel version:
  php artisan --version

List all routes:
  php artisan route:list

FOLDER STRUCTURE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
app/                    # Laravel application code
├── Http/Controllers/   # Controllers
├── Models/            # Database models
└── Services/          # Business logic

resources/             # Frontend resources
├── js/               # Vue components
├── css/              # Stylesheets
└── views/            # Blade templates

database/             # Database files
├── migrations/       # Database migrations
└── seeders/          # Database seeders

public/               # Public assets
├── storage/          # Uploaded files (symlink)
└── build/            # Compiled assets

storage/              # Storage directory
├── app/              # Application files
├── framework/        # Framework files
└── logs/             # Log files

installer/            # Installation scripts
├── install-esign.bat # Installer launcher
├── install-esign.ps1 # Main installer
├── shortcuts/        # Helper scripts
└── config/           # Configuration templates

PORTS USED
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
8000  - Laravel application server
5432  - PostgreSQL database
5173  - Vite development server (dev mode only)

LOGS LOCATION
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Application logs: storage/logs/laravel.log
Installation log: installer/install.log (if exists)

BACKUP & RESTORE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Backup database:
  pg_dump -U postgres -d esign > backup.sql

Restore database:
  psql -U postgres -d esign < backup.sql

Backup uploaded files:
  Copy storage/app/ folder

SUPPORT
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
For technical support:
- Check documentation: README.md
- Check installation guide: INSTALLATION.md
- Open GitHub issue for bugs
- Contact system administrator

VERSION INFORMATION
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
E-Sign System v1.0
Installer v1.0
Last updated: 2026-10-19

LICENSE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
MIT License - See LICENSE file for details
