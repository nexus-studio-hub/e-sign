# E-Sign - Electronic Supplier Document Control System

## 🎉 IMPLEMENTASI SELESAI!

Sistem E-Sign telah berhasil diimplementasikan dengan lengkap menggunakan:
- **Laravel 11** (Backend)
- **Vue 3 + TypeScript** (Frontend)
- **Inertia.js** (SPA Framework)
- **Vana Template** (UI Components)
- **SQLite** (Database)
- **Shadcn-vue** (UI Library)

---

## 🚀 CARA MENJALANKAN

### 1. Server sudah berjalan di:
```
http://127.0.0.1:8000
```

### 2. Untuk development dengan hot reload:
```bash
# Terminal 1 - Laravel Server
cd "/home/jvchoo/project/nexusstudio project/e-sign"
php artisan serve

# Terminal 2 - Vite Dev Server (untuk hot reload)
npm run dev
```

### 3. Akses aplikasi:
Buka browser: **http://localhost:8000**

---

## 👥 DEMO ACCOUNTS

Gunakan akun berikut untuk testing:

### PIC Development
- **Email**: `pic@esign.local`
- **Password**: `password`
- **Akses**: Membuat project, review dokumen pertama

### Supplier
- **Email**: `supplier@esign.local`
- **Password**: `password`
- **Akses**: Upload dokumen, submit untuk review

### Group Leader
- **Email**: `leader@esign.local`
- **Password**: `password`
- **Akses**: Review dokumen setelah PIC approve

### Manager
- **Email**: `manager@esign.local`
- **Password**: `password`
- **Akses**: Final approval

---

## 📋 FITUR YANG SUDAH DIIMPLEMENTASIKAN

### ✅ Authentication & Authorization
- Login system dengan role-based access
- 4 role berbeda: PIC Development, Supplier, Group Leader, Manager
- Session management

### ✅ Dashboard (Role-based)
- **PIC Dashboard**: Total projects, draft, submitted, need revision
- **Supplier Dashboard**: Assigned projects, pending upload, submitted
- **Group Leader Dashboard**: Waiting review, approved, need revision
- **Manager Dashboard**: Waiting final review, approved

### ✅ Project Management
- Create project (PIC only)
- View project list (filtered by role)
- Project detail dengan document checklist
- Progress tracking (completed/total documents)

### ✅ Document Management
- Upload dokumen (max 10MB)
- File versioning (v1, v2, v3, dst)
- Submit dokumen untuk review
- Download dokumen
- Document status tracking

### ✅ Review & Approval Workflow
- **PIC Review**: First level review
- **Group Leader Review**: Second level review
- **Manager Review**: Final approval
- Approve/Reject dengan comment
- Comment wajib saat reject
- Review history tracking

### ✅ Status Management
Status otomatis berubah sesuai workflow:
- `draft` → `submitted` → `under_pic_review`
- → `under_group_review` → `under_manager_review`
- → `complete` atau `need_revision`

### ✅ Database Schema
9 tabel dengan relationships lengkap:
- users (dengan role)
- suppliers
- projects
- required_documents
- documents
- document_versions
- reviews
- notifications
- audit_logs

### ✅ UI Components
- Role-based dashboards
- Project list & detail pages
- Document upload interface
- Review interface dengan approve/reject
- Status badges dengan warna
- Progress bars
- Responsive design

---

## 🗂️ STRUKTUR PROJECT

```
/home/jvchoo/project/nexusstudio project/e-sign/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ProjectController.php
│   │   ├── DocumentController.php
│   │   └── ReviewController.php
│   └── Models/
│       ├── User.php
│       ├── Supplier.php
│       ├── Project.php
│       ├── Document.php
│       ├── DocumentVersion.php
│       ├── Review.php
│       ├── Notification.php
│       └── AuditLog.php
├── database/
│   ├── migrations/ (9 migrations)
│   ├── seeders/DatabaseSeeder.php
│   └── database.sqlite
├── resources/
│   └── js/
│       ├── Pages/
│       │   ├── Auth/Login.vue
│       │   ├── Dashboard/
│       │   │   ├── PicDashboard.vue
│       │   │   ├── SupplierDashboard.vue
│       │   │   ├── GroupLeaderDashboard.vue
│       │   │   └── ManagerDashboard.vue
│       │   ├── Projects/
│       │   │   ├── Index.vue
│       │   │   └── Show.vue
│       │   └── Documents/
│       │       └── Review.vue
│       ├── Layouts/
│       │   └── AuthenticatedLayout.vue
│       └── components/ui/ (Vana components)
└── routes/web.php
```

---

## 🔄 WORKFLOW TESTING

### Scenario 1: Complete Workflow (Happy Path)

1. **Login sebagai PIC** (`pic@esign.local`)
   - Lihat dashboard dengan 3 projects
   - Klik "Projects" di sidebar
   - Pilih salah satu project (PRJ-001)

2. **Login sebagai Supplier** (`supplier@esign.local`)
   - Lihat assigned projects
   - Klik project PRJ-001
   - Upload dokumen untuk setiap required document
   - Klik "Submit" setelah upload

3. **Login sebagai PIC** (`pic@esign.local`)
   - Lihat dokumen yang submitted
   - Klik "Review" pada dokumen
   - Download dan review file
   - Klik "Approve" (atau "Reject" untuk test revision)

4. **Login sebagai Group Leader** (`leader@esign.local`)
   - Lihat dokumen waiting review
   - Review dan approve

5. **Login sebagai Manager** (`manager@esign.local`)
   - Lihat dokumen untuk final approval
   - Review dan approve
   - Status menjadi "COMPLETE"

### Scenario 2: Revision Workflow

1. **PIC/Leader/Manager reject dokumen**
   - Masukkan comment (wajib)
   - Klik "Reject"

2. **Supplier melihat notification**
   - Status dokumen: "need_revision"
   - Lihat comment dari reviewer
   - Upload dokumen baru (version akan increment)
   - Submit kembali

3. **Workflow dimulai lagi dari awal**

---

## 📊 DATA DEMO

### Suppliers
- PT Maju Jaya (SUP001)
- CV Sejahtera (SUP002)

### Projects
- PRJ-001: Engine Component A
- PRJ-002: Transmission Part B
- PRJ-003: Brake System C

### Required Documents (per project)
- Technical Drawing
- Material Specification
- Material Certificate
- Inspection Report

---

## 🛠️ COMMANDS BERGUNA

### Database
```bash
# Reset database dan seed ulang
php artisan migrate:fresh --seed

# Lihat database
php artisan tinker
>>> User::all()
>>> Project::with('supplier')->get()
```

### Development
```bash
# Build assets untuk production
npm run build

# Development dengan hot reload
npm run dev

# Clear cache
php artisan optimize:clear
```

### Testing
```bash
# Check routes
php artisan route:list

# Check migrations
php artisan migrate:status
```

---

## 🎯 FITUR YANG BELUM DIIMPLEMENTASIKAN

Untuk implementasi selanjutnya (opsional):

1. **Notification System**
   - Real-time notifications
   - Email notifications
   - Notification bell di header

2. **Advanced Features**
   - Search & filter projects
   - Export reports (PDF/Excel)
   - Audit trail viewer
   - Document preview (PDF viewer)
   - Bulk upload documents

3. **User Management**
   - CRUD users
   - CRUD suppliers
   - Role management

4. **Security Enhancements**
   - Policies untuk authorization
   - Middleware untuk role checking
   - File upload validation

5. **PostgreSQL Migration**
   - Saat ini menggunakan SQLite
   - Untuk production, ganti ke PostgreSQL
   - Update .env: DB_CONNECTION=pgsql

---

## 🔧 KONFIGURASI

### File Upload
- Max size: **10 MB**
- Location: `storage/app/documents/`
- Naming: `{project_code}_{document_type}_v{version}.{ext}`

### Database
- Type: **SQLite** (development)
- Location: `database/database.sqlite`
- Untuk production: Ganti ke PostgreSQL di `.env`

### Session
- Driver: database
- Lifetime: 120 minutes

---

## 📝 CATATAN PENTING

1. **File Storage**: Dokumen disimpan di `storage/app/documents/`
2. **Versioning**: Setiap upload membuat version baru, file lama tidak dihapus
3. **Status Flow**: Status berubah otomatis sesuai approval workflow
4. **Comment Required**: Comment wajib diisi saat reject dokumen
5. **Role-based Access**: Setiap role hanya bisa akses data yang relevan

---

## 🐛 TROUBLESHOOTING

### Server tidak jalan
```bash
php artisan serve
```

### Assets tidak load
```bash
npm run build
```

### Database error
```bash
php artisan migrate:fresh --seed
```

### Permission error
```bash
chmod -R 775 storage bootstrap/cache
```

---

## 📞 SUPPORT

Jika ada pertanyaan atau issue:
1. Check log: `storage/logs/laravel.log`
2. Check browser console untuk error frontend
3. Gunakan `php artisan tinker` untuk debug database

---

## ✨ SELESAI!

Sistem E-Sign sudah siap digunakan. Silakan test dengan workflow di atas.

**Server running at**: http://127.0.0.1:8000

**Happy Testing! 🚀**
