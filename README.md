# E-Sign - Electronic Supplier Document Control System

Sistem digital untuk mengelola proses pengiriman dokumen dari Supplier dengan multi-level approval workflow.

## Quick Start

```bash
# 1. Install dependencies
composer install
npm install --legacy-peer-deps

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Setup database
touch database/database.sqlite
php artisan migrate:fresh --seed

# 4. Build assets
npm run build

# 5. Run server
php artisan serve
```

Akses: http://localhost:8000

## Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| PIC Development | pic@esign.local | password |
| Supplier | supplier@esign.local | password |
| Group Leader | leader@esign.local | password |
| Manager | manager@esign.local | password |

## Tech Stack

- Laravel 11
- Vue 3 + TypeScript
- Inertia.js
- Vana Template
- Shadcn-vue
- SQLite

## Documentation

Lihat [IMPLEMENTATION_GUIDE.md](IMPLEMENTATION_GUIDE.md) untuk dokumentasi lengkap.

## License

MIT
