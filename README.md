# E-Sign System

A comprehensive document management system with digital signature workflow, multi-level approval process, and QR code verification.

## Features

- 📄 **Document Management** - Upload, version control, and organize documents
- ✅ **Multi-Level Approval** - Configurable approval workflow (PIC → Group Leader → Manager)
- 🔐 **Digital Signatures** - Secure document signing with barcode stamping
- 📱 **QR Code Verification** - Verify document authenticity via QR code
- 👥 **Role-Based Access Control** - Superadmin, Reviewer, and Supplier roles
- 📊 **Dashboard & Analytics** - Track document status and approval progress
- 🔔 **Notifications** - Real-time notifications for document updates
- 📝 **Audit Trail** - Complete history of document changes and approvals

## Quick Start (Windows)

### Prerequisites
- Windows 10/11 (64-bit)
- Administrator access
- Internet connection
- 8GB RAM minimum
- 5GB free disk space

### Installation

1. **Clone or download this repository**
   ```bash
   git clone <repository-url>
   cd e-sign
   ```

2. **Run the installer**
   - Navigate to `installer/` folder
   - Right-click `install-esign.bat`
   - Select "Run as administrator"
   - Follow on-screen instructions

3. **Start the application**
   - Double-click "Start E-Sign Server" on desktop
   - Open browser to http://localhost:8000
   - Login with: `admin@esign.local` / `password`

**Installation takes 10-15 minutes** and will automatically install:
- PHP 8.3
- Composer
- Node.js 18 LTS
- PostgreSQL 16
- All project dependencies

## Tech Stack

- **Backend:** Laravel 11, PHP 8.3
- **Frontend:** Vue 3, TypeScript, Inertia.js
- **UI:** Shadcn-vue, Tailwind CSS
- **Database:** PostgreSQL 16
- **Build Tools:** Vite, npm

## System Requirements

### Minimum
- Windows 10/11 (64-bit)
- 8GB RAM
- 5GB free disk space
- Dual-core processor

### Recommended
- Windows 11 (64-bit)
- 16GB RAM
- 10GB free disk space
- Quad-core processor
- SSD storage

## Default Credentials

**Superadmin:**
- Email: `admin@esign.local`
- Password: `password`

**Database:**
- Host: `127.0.0.1`
- Port: `5432`
- Database: `esign`
- Username: `postgres`
- Password: `postgres`

⚠️ **Change default passwords immediately after first login!**

## Desktop Shortcuts

After installation, you'll find these shortcuts on your desktop:

- **Start E-Sign Server** - Start the application server
- **Start E-Sign Dev** - Start development mode with hot-reload
- **Stop E-Sign** - Stop all services

## Development

### Start Development Mode
```bash
# Double-click "Start E-Sign Dev" shortcut
# Or run manually:
php artisan serve
npm run dev
```

### Project Structure
```
app/                    # Laravel application
├── Http/Controllers/   # Controllers
├── Models/            # Database models
└── Services/          # Business logic

resources/             # Frontend resources
├── js/               # Vue components
├── css/              # Stylesheets
└── views/            # Blade templates

database/             # Database
├── migrations/       # Migrations
└── seeders/          # Seeders

installer/            # Installation scripts
├── install-esign.bat # Installer launcher
├── install-esign.ps1 # Main installer
└── shortcuts/        # Helper scripts
```

### Useful Commands

```bash
# Start server
php artisan serve

# Run migrations
php artisan migrate

# Reset database
php artisan migrate:fresh --seed

# Build frontend
npm run build

# Development mode
npm run dev

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Updating

To update to the latest version:

1. Stop all E-Sign services
2. Run `update-esign.bat` in project root
3. Restart E-Sign Server

## Troubleshooting

### Server won't start
- Check if port 8000 is in use: `netstat -ano | findstr :8000`
- Run "Stop E-Sign" shortcut first
- Restart your computer

### Database connection error
- Check if PostgreSQL service is running
- Open Services (Win+R → `services.msc`)
- Start "postgresql-x64-16" service

### Frontend not loading
- Run `npm run build`
- Clear browser cache (Ctrl+Shift+Delete)
- Hard refresh (Ctrl+F5)

For more troubleshooting, see `installer/README.txt`

## Documentation

- [Installation Guide](INSTALLATION.md) - Detailed installation instructions
- [User Manual](installer/README.txt) - Complete user guide
- [API Documentation](#) - API reference (coming soon)

## Security

- Change default passwords immediately
- Set `APP_DEBUG=false` in production
- Set `APP_ENV=production` in production
- Use HTTPS in production
- Keep system updated regularly

## License

MIT License - see [LICENSE](LICENSE) file for details

## Support

For issues and questions:
- Check [Installation Guide](INSTALLATION.md)
- Check [User Manual](installer/README.txt)
- Open a GitHub issue
- Contact system administrator

## Credits

Developed with ❤️ using Laravel, Vue.js, and modern web technologies.

---

**Version:** 1.0  
**Last Updated:** 2026-10-19
