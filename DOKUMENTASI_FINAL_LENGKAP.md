# 📚 **DOKUMENTASI FINAL LENGKAP - TIGA PUTRA MANAGEMENT SYSTEM**

## 🎯 **OVERVIEW SISTEM**

**Tiga Putra Management System** adalah sistem manajemen perusahaan yang dirancang khusus untuk mengelola karyawan, absensi, gaji, dan operasional perusahaan. Sistem ini menggunakan **Laravel 10** dengan database **MySQL** dan memiliki **2 level akses**: **Manager** (akses penuh) dan **Admin** (akses terbatas).

---

## 🏗️ **STRUKTUR DATABASE**

### **Tabel Utama:**
1. **`users`** - Data pengguna sistem (admin/manager)
2. **`employees`** - Data karyawan (karyawan/mandor)
3. **`gudangs`** - Data karyawan gudang (warehouse employees)
4. **`mandors`** - Data mandor
5. **`absensis`** - Data absensi karyawan
6. **`lokasis`** - Data lokasi kerja
7. **`kandangs`** - Data kandang
8. **`pembibitans`** - Data pembibitan
9. **`salary_reports`** - Laporan gaji
10. **`monthly_attendance_reports`** - Laporan absensi bulanan
11. **`calendar_attendances`** - Kalender absensi

### **Relasi Database:**
```
users (1) -----> (many) absensis
employees (1) -----> (many) absensis
employees (many) <-----> (many) kandangs
gudangs (1) -----> (1) lokasis
kandangs (1) -----> (1) lokasis
pembibitans (1) -----> (1) lokasis
pembibitans (1) -----> (1) kandangs
```

---

## 👥 **SISTEM ROLE & PERMISSION**

### **🔑 MANAGER (Akses Penuh)**
- ✅ **Master Data:** Semua CRUD operations
- ✅ **Karyawan:** Tambah, edit, hapus semua jenis karyawan
- ✅ **Absensi:** Kelola absensi semua karyawan
- ✅ **Laporan:** Generate dan export semua laporan
- ✅ **Kalender:** Kelola kalender absensi
- ✅ **Gaji:** Generate laporan gaji lengkap

### **🔒 ADMIN (Akses Terbatas)**
- ✅ **Master Data:** Hanya lihat dan edit (tidak bisa hapus)
- ✅ **Karyawan:** Hanya karyawan biasa (bukan mandor)
- ✅ **Absensi:** Hanya untuk karyawan biasa
- ✅ **Laporan:** Hanya lihat dan export (tidak bisa generate)
- ✅ **Kalender:** Hanya lihat
- ❌ **Mandor:** Tidak bisa akses sama sekali
- ❌ **Hapus Data:** Tidak bisa hapus data apapun

---

## 🚀 **FITUR UTAMA SISTEM**

### **1. 📊 DASHBOARD & STATISTICS**
- **Real-time Statistics Cards** - Data statistik real-time
- **Professional UI/UX** - Tema corporate dengan animasi
- **Responsive Design** - Mobile-friendly
- **Real-time Clock** - Waktu Jakarta/Asia

### **2. 👤 MANAJEMEN KARYAWAN**

#### **Master Karyawan (`employees`)**
- **Field:** `nama`, `gaji`, `role` (karyawan/mandor)
- **Fitur:** CRUD lengkap, search, pagination
- **Role-based:** Admin hanya lihat karyawan biasa

#### **Master Karyawan Gudang (`gudangs`)**
- **Field:** `nama`, `gaji` (warehouse employees)
- **Fitur:** CRUD lengkap, search, pagination
- **Statistics:** Total karyawan gudang, total gaji

#### **Master Mandor (`mandors`)**
- **Field:** `nama`, `gaji`, `lokasi_id`
- **Fitur:** CRUD lengkap (hanya Manager)
- **Admin:** Tidak bisa akses sama sekali

### **3. ⏰ SISTEM ABSENSI**

#### **Master Absensi (`absensis`)**
- **Field:** `employee_id`, `tanggal`, `status` (full/setengah_hari/absen)
- **Fitur:** 
  - Real-time data sync
  - Status update via AJAX
  - Search dan filter
  - Role-based access

#### **Kalender Absensi (`calendar_attendances`)**
- **Fitur:**
  - Kalender real-time dengan hari Indonesia
  - Input tahun fleksibel (2020-2030)
  - Status absensi per hari
  - Timezone Jakarta/Asia
  - Legend dan statistik per karyawan

### **4. 📍 MASTER DATA LOKASI & KANDANG**

#### **Master Lokasi (`lokasis`)**
- **Field:** `nama_lokasi`, `alamat`, `deskripsi`
- **Fitur:** CRUD lengkap, search

#### **Master Kandang (`kandangs`)**
- **Field:** `nama_kandang`, `lokasi_id`, `deskripsi`
- **Fitur:** CRUD lengkap, relasi dengan lokasi
- **Many-to-Many:** Relasi dengan employees

### **5. 🌱 SISTEM PEMBIBITAN**

#### **Master Pembibitan (`pembibitans`)**
- **Field:** `judul`, `lokasi_id`, `kandang_id`, `tanggal_mulai`
- **Fitur:** CRUD lengkap, relasi dengan lokasi dan kandang
- **Search:** Berdasarkan judul, lokasi, kandang

### **6. 📈 SISTEM LAPORAN**

#### **Laporan Gaji (`salary_reports`)**
- **Fitur:**
  - Generate laporan per bulan/tahun
  - Export PDF/Excel
  - Filter berdasarkan karyawan, lokasi, kandang
  - Real-time data sync
  - Role-based access

#### **Laporan Absensi Bulanan (`monthly_attendance_reports`)**
- **Fitur:**
  - Generate laporan otomatis
  - Export PDF
  - Statistik kehadiran
  - Persentase kehadiran per karyawan

### **7. 🎨 UI/UX FEATURES**

#### **Professional Theme**
- **Color Scheme:** Corporate blue-gray gradient
- **Typography:** Professional fonts
- **Animations:** Smooth transitions dan hover effects
- **Icons:** Bootstrap Icons konsisten

#### **Interactive Elements**
- **Real-time Clock:** Jakarta timezone
- **AJAX Updates:** Status absensi real-time
- **Modal Forms:** Professional modal design
- **Table Scroll:** Horizontal dan vertical scroll
- **Search & Filter:** Real-time search

#### **Responsive Design**
- **Mobile-friendly:** Semua fitur responsive
- **Touch-friendly:** Proper spacing untuk mobile
- **Cross-browser:** Compatible dengan semua browser

---

## 🔧 **INSTALASI & SETUP**

### **1. Requirements**
- PHP 8.1+
- MySQL 8.0+
- Composer
- Node.js & NPM
- Laravel 10

### **2. Installation Steps**
```bash
# Clone repository
git clone [repository-url]
cd Managemen

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
# Edit .env file:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Build assets
npm run build

# Start server
php artisan serve
```

### **3. Default Login Credentials**
- **Manager:** `manager@tigaputra.com` / `password`
- **Admin:** `admin@tigaputra.com` / `password`

---

## 📱 **GUIDE PENGGUNAAN**

### **🔑 LOGIN & AUTHENTICATION**

#### **Halaman Login**
- **URL:** `/login`
- **Fitur:**
  - Professional login form
  - Animated background
  - Circular logo
  - Remember me option
  - Error handling

#### **Role-based Redirect**
- **Manager:** Redirect ke `/manager/gudangs`
- **Admin:** Redirect ke `/admin/gudangs`

### **📊 DASHBOARD MANAGER**

#### **Navigation Menu**
1. **Master Gudang** - Kelola karyawan gudang
2. **Master Mandor** - Kelola mandor
3. **Master Absensi** - Kelola absensi
4. **Master Pembibitan** - Kelola pembibitan
5. **Master Kandang** - Kelola kandang
6. **Master Lokasi** - Kelola lokasi
7. **Laporan Absensi Bulanan** - Generate laporan
8. **Kalender Absensi** - Kalender real-time
9. **Laporan Gaji** - Generate laporan gaji

#### **Statistics Cards**
- **Total Karyawan Gudang** - Jumlah karyawan gudang
- **Total Gaji** - Total gaji semua karyawan
- **Real-time Data** - Update otomatis

### **📊 DASHBOARD ADMIN**

#### **Navigation Menu (Terbatas)**
1. **Master Gudang** - Lihat/edit karyawan gudang
2. **Master Absensi** - Lihat/edit absensi karyawan
3. **Master Pembibitan** - Lihat/edit pembibitan
4. **Master Kandang** - Lihat/edit kandang
5. **Master Lokasi** - Lihat/edit lokasi
6. **Master Karyawan** - Lihat/edit karyawan biasa
7. **Manage** - Halaman khusus admin
8. **Laporan** - Lihat dan export laporan

#### **Restrictions**
- ❌ Tidak bisa akses Master Mandor
- ❌ Tidak bisa hapus data apapun
- ❌ Tidak bisa generate laporan
- ❌ Tidak bisa lihat data mandor

### **👥 MANAJEMEN KARYAWAN**

#### **Tambah Karyawan Gudang**
1. Klik **"Tambah Karyawan Gudang"**
2. Isi form:
   - **Nama Karyawan Gudang** (required)
   - **Gaji** (required, format Rupiah)
3. Klik **"Simpan"**

#### **Edit Karyawan Gudang**
1. Klik icon **Edit** (pensil) di tabel
2. Ubah data yang diperlukan
3. Klik **"Simpan"**

#### **Lihat Detail Karyawan Gudang**
1. Klik icon **Lihat** (mata) di tabel
2. Tampil detail lengkap karyawan

### **⏰ SISTEM ABSENSI**

#### **Tambah Absensi**
1. Klik **"Tambah Absensi"**
2. Pilih **Karyawan** dari dropdown
3. Pilih **Tanggal**
4. Pilih **Status** (Full Day/Setengah Hari/Absen)
5. Klik **"Simpan"**

#### **Kalender Absensi**
1. Klik **"Kalender Absensi"**
2. Pilih **Tahun** (2020-2030)
3. Pilih **Bulan** (1-12)
4. Pilih **Tipe Karyawan** (All/Karyawan/Mandor)
5. Klik **"Cari"**

#### **Update Status Absensi**
1. Di kalender, klik **hari** yang ingin diupdate
2. Pilih **status** baru
3. Status terupdate real-time

### **📈 SISTEM LAPORAN**

#### **Generate Laporan Gaji**
1. Klik **"Laporan Gaji"**
2. Pilih **Tahun** dan **Bulan**
3. Klik **"Generate"**
4. Download **PDF/Excel**

#### **Export Laporan**
1. Di halaman laporan, klik **"Export"**
2. Pilih format: **PDF** atau **Excel**
3. Download file

---

## 🛠️ **TECHNICAL SPECIFICATIONS**

### **Backend Technologies**
- **Framework:** Laravel 10
- **Database:** MySQL 8.0
- **ORM:** Eloquent
- **Authentication:** Laravel Sanctum
- **Validation:** Laravel Validation
- **Migrations:** Database versioning

### **Frontend Technologies**
- **CSS Framework:** Bootstrap 5.3
- **Icons:** Bootstrap Icons
- **JavaScript:** Vanilla JS + AJAX
- **Build Tool:** Vite
- **Styling:** Custom CSS dengan professional theme

### **Database Features**
- **Migrations:** 15+ migration files
- **Seeders:** Data awal untuk testing
- **Relationships:** Complex Eloquent relationships
- **Indexing:** Optimized database queries
- **Foreign Keys:** Data integrity

### **Security Features**
- **CSRF Protection:** All forms protected
- **SQL Injection:** Eloquent ORM protection
- **XSS Protection:** Blade templating
- **Role-based Access:** Middleware protection
- **Input Validation:** Server-side validation

---

## 📋 **API ENDPOINTS**

### **Authentication**
- `POST /login` - User login
- `POST /logout` - User logout

### **Manager Routes** (`/manager/`)
- `GET /gudangs` - List karyawan gudang
- `POST /gudangs` - Create karyawan gudang
- `PUT /gudangs/{id}` - Update karyawan gudang
- `DELETE /gudangs/{id}` - Delete karyawan gudang
- `GET /absensis` - List absensi
- `POST /absensis` - Create absensi
- `GET /salary-reports/export` - Export laporan gaji

### **Admin Routes** (`/admin/`)
- `GET /gudangs` - List karyawan gudang (read-only)
- `POST /gudangs` - Create karyawan gudang
- `PUT /gudangs/{id}` - Update karyawan gudang
- `GET /salary-reports/export` - Export laporan gaji

---

## 🐛 **TROUBLESHOOTING**

### **Common Issues**

#### **1. Login Error**
- **Problem:** "Kredensial tidak cocok"
- **Solution:** Jalankan `php artisan db:seed --class=UserSeeder`

#### **2. Database Error**
- **Problem:** "Column not found"
- **Solution:** Jalankan `php artisan migrate:fresh --seed`

#### **3. Permission Error**
- **Problem:** "403 Unauthorized"
- **Solution:** Check role middleware dan user permissions

#### **4. Asset Error**
- **Problem:** CSS/JS tidak load
- **Solution:** Jalankan `npm run build`

### **Debug Commands**
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check routes
php artisan route:list

# Check database
php artisan tinker
DB::select('SHOW TABLES');

# Check logs
tail -f storage/logs/laravel.log
```

---

## 🚀 **DEPLOYMENT GUIDE**

### **Production Setup**

#### **1. Server Requirements**
- PHP 8.1+ dengan extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- MySQL 8.0+
- Web Server (Apache/Nginx)
- SSL Certificate

#### **2. Environment Configuration**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
```

#### **3. Deployment Steps**
```bash
# Upload files
# Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Database setup
php artisan migrate --force
php artisan db:seed --force

# Cache optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### **4. cPanel Deployment**
1. Upload files ke `public_html`
2. Setup MySQL database
3. Edit `.env` file
4. Run migrations via terminal
5. Set proper permissions

---

## 📞 **SUPPORT & MAINTENANCE**

### **Regular Maintenance**
- **Database Backup:** Daily automated backup
- **Log Monitoring:** Check error logs regularly
- **Performance:** Monitor query performance
- **Security:** Update dependencies regularly

### **Contact Information**
- **Developer:** AI Assistant
- **Version:** 1.0.0
- **Last Updated:** October 2025
- **Documentation:** This file

---

## 🎉 **FINAL NOTES**

**Tiga Putra Management System** telah dikembangkan dengan standar profesional tinggi, menggunakan teknologi modern dan best practices. Sistem ini siap untuk production dan dapat dikembangkan lebih lanjut sesuai kebutuhan bisnis.

### **Key Features Summary:**
- ✅ **Professional UI/UX** dengan tema corporate
- ✅ **Role-based Access Control** yang ketat
- ✅ **Real-time Data Sync** antar semua fitur
- ✅ **Comprehensive Reporting** dengan export PDF/Excel
- ✅ **Mobile-responsive Design**
- ✅ **Database Integrity** dengan proper relationships
- ✅ **Security Features** yang lengkap
- ✅ **Scalable Architecture** untuk pengembangan masa depan

**Sistem siap digunakan untuk mengelola operasional perusahaan dengan efisien dan profesional! 🚀**

---

*Dokumentasi ini dibuat untuk memudahkan pengguna memahami dan menggunakan sistem Tiga Putra Management dengan optimal.*
