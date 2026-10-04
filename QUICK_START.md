# 🚀 E-Sign Quick Start Guide

## ✅ Status: READY TO USE!

Server sudah berjalan di: **http://127.0.0.1:8000**

---

## 🎯 Cara Menggunakan

### 1. Akses Aplikasi
Buka browser dan kunjungi: **http://127.0.0.1:8000**

### 2. Login dengan Demo Account

| Role | Email | Password |
|------|-------|----------|
| **PIC Development** | pic@esign.local | password |
| **Supplier** | supplier@esign.local | password |
| **Group Leader** | leader@esign.local | password |
| **Manager** | manager@esign.local | password |

---

## 🔄 Menjalankan Server

### Cara Mudah (Recommended)
```bash
cd "/home/jvchoo/project/nexusstudio project/e-sign"
./START_SERVER.sh
```

### Cara Manual
```bash
cd "/home/jvchoo/project/nexusstudio project/e-sign"

# Terminal 1 - Laravel Server
php artisan serve --host=127.0.0.1 --port=8000

# Terminal 2 - Vite Dev Server (optional, untuk hot reload)
npm run dev
```

---

## 🛑 Menghentikan Server

```bash
pkill -9 -f "php artisan serve"
pkill -9 -f "vite"
```

---

## 📝 Test Workflow Lengkap

### Scenario 1: Complete Approval Flow

1. **Login sebagai PIC** (pic@esign.local)
   - Lihat dashboard dengan 3 projects
   - Klik menu "Projects" di sidebar
   - Pilih project "PRJ-001"

2. **Login sebagai Supplier** (supplier@esign.local)
   - Lihat project yang assigned
   - Klik project "PRJ-001"
   - Upload dokumen untuk setiap required document
   - Klik tombol "Submit" setelah upload

3. **Login sebagai PIC** (pic@esign.local)
   - Lihat dokumen yang submitted
   - Klik "Review" pada dokumen
   - Download dan review file
   - Klik "Approve"

4. **Login sebagai Group Leader** (leader@esign.local)
   - Lihat dokumen waiting review
   - Review dan approve

5. **Login sebagai Manager** (manager@esign.local)
   - Lihat dokumen untuk final approval
   - Review dan approve
   - Status menjadi "COMPLETE" ✅

### Scenario 2: Revision Flow

1. **Reviewer reject dokumen**
   - Masukkan comment (wajib)
   - Klik "Reject"

2. **Supplier** melihat dan revisi
   - Status dokumen: "need_revision"
   - Lihat comment dari reviewer
   - Upload dokumen baru (version akan increment)
   - Submit kembali

3. **Workflow dimulai lagi dari awal**

---

## 🔧 Troubleshooting

### Login button tidak bisa diklik?
```bash
# Clear cache
cd "/home/jvchoo/project/nexusstudio project/e-sign"
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Restart server
./START_SERVER.sh
```

### Server tidak jalan?
```bash
# Cek apakah port 8000 sudah digunakan
netstat -tuln | grep 8000

# Kill process yang menggunakan port 8000
pkill -9 -f "php artisan serve"

# Start ulang
./START_SERVER.sh
```

### Database error?
```bash
cd "/home/jvchoo/project/nexusstudio project/e-sign"
php artisan migrate:fresh --seed
```

### Assets tidak load?
```bash
cd "/home/jvchoo/project/nexusstudio project/e-sign"
npm run build
```

---

## 📊 Data Demo

### Projects
- **PRJ-001**: Engine Component A
- **PRJ-002**: Transmission Part B
- **PRJ-003**: Brake System C

### Required Documents (per project)
- Technical Drawing
- Material Specification
- Material Certificate
- Inspection Report

---

## 📁 File Penting

```
/home/jvchoo/project/nexusstudio project/e-sign/
├── START_SERVER.sh          # Script untuk start server
├── README.md                # Quick reference
├── IMPLEMENTATION_GUIDE.md  # Dokumentasi lengkap
├── database/database.sqlite # Database dengan demo data
└── storage/app/documents/   # Lokasi file upload
```

---

## 🎯 Fitur yang Sudah Berfungsi

✅ Login & Authentication  
✅ Role-based Dashboard (4 roles)  
✅ Project Management  
✅ Document Upload (max 10MB)  
✅ File Versioning  
✅ Multi-level Approval Workflow  
✅ Status Tracking  
✅ Review History  
✅ Revision Workflow  
✅ Responsive UI  

---

## 📞 Bantuan

Jika ada masalah:

1. **Cek log Laravel**: `tail -f /tmp/laravel-server.log`
2. **Cek log Vite**: `tail -f /tmp/vite-dev.log`
3. **Cek browser console** untuk error JavaScript
4. **Restart server**: `./START_SERVER.sh`

---

## ✨ Selamat Menggunakan E-Sign System!

**URL**: http://127.0.0.1:8000  
**Status**: ✅ READY

