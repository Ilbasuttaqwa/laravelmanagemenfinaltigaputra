# Employee Management System

Sistem manajemen karyawan dengan multi-user (Admin dan Manager) menggunakan Laravel.

## Fitur

- **Multi-User Authentication**: Admin dan Manager dengan role-based access
- **CRUD Master Karyawan**: Create, Read, Update, Delete data karyawan
- **Dashboard**: Dashboard terpisah untuk Admin dan Manager
- **Role-Based Access Control**: Admin dapat menghapus karyawan, Manager tidak
- **Search & Filter**: Pencarian berdasarkan nama/NIK dan filter berdasarkan status/departemen
- **Responsive Design**: Interface yang responsive menggunakan Bootstrap 5

## Struktur Database

### Users Table
- `id`: Primary key
- `name`: Nama user
- `email`: Email (unique)
- `password`: Password (hashed)
- `role`: Role user (admin/manager)
- `created_at`, `updated_at`: Timestamps

### Employees Table
- `id`: Primary key
- `nik`: Nomor Induk Karyawan (unique)
- `nama`: Nama lengkap
- `email`: Email (unique)
- `telepon`: Nomor telepon
- `alamat`: Alamat lengkap
- `jenis_kelamin`: Jenis kelamin (L/P)
- `tanggal_lahir`: Tanggal lahir
- `tanggal_masuk`: Tanggal masuk kerja
- `jabatan`: Jabatan
- `departemen`: Departemen
- `gaji`: Gaji (decimal)
- `status`: Status karyawan (aktif/tidak_aktif)
- `created_at`, `updated_at`: Timestamps

## Setup dan Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd Managemen
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration
Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=managemen
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Database Migration
```bash
# Buat database baru
mysql -u root -p
CREATE DATABASE managemen;

# Jalankan migrasi
php artisan migrate
```

### 6. Seed Database
```bash
php artisan db:seed
```

### 7. Serve Application
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## Default Login Credentials

### Admin
- **Email**: admin@company.com
- **Password**: password

### Manager
- **Email**: manager@company.com
- **Password**: password

## Struktur Aplikasi

### Routes
- **Authentication**: `/login`, `/logout`
- **Admin Routes**: `/admin/*` (dashboard, employees CRUD)
- **Manager Routes**: `/manager/*` (dashboard, employees CRUD tanpa delete)

### Controllers
- `Auth\LoginController`: Handle authentication
- `DashboardController`: Dashboard untuk admin dan manager
- `EmployeeController`: CRUD operations untuk karyawan

### Middleware
- `CheckRole`: Middleware untuk role-based access control

### Models
- `User`: Model untuk user dengan role system
- `Employee`: Model untuk data karyawan

### Views
- `auth/login.blade.php`: Halaman login
- `dashboard/admin.blade.php`: Dashboard admin
- `dashboard/manager.blade.php`: Dashboard manager
- `employees/*.blade.php`: Views untuk CRUD karyawan
- `layouts/app.blade.php`: Layout utama

## Fitur Detail

### Admin Features
- Dashboard dengan statistik lengkap
- Full CRUD karyawan (Create, Read, Update, Delete)
- Akses ke semua data karyawan
- Manajemen departemen

### Manager Features
- Dashboard dengan statistik terbatas
- CRUD karyawan tanpa delete
- Akses ke data karyawan
- View departemen

### Employee Management
- Tambah karyawan baru dengan validasi lengkap
- Edit data karyawan
- Hapus karyawan (hanya admin)
- Detail karyawan dengan ringkasan
- Search dan filter karyawan
- Pagination untuk performa

### Security Features
- Role-based access control
- CSRF protection
- Input validation
- SQL injection protection
- XSS protection

## Teknologi yang Digunakan

- **Backend**: Laravel 10
- **Frontend**: Bootstrap 5, Bootstrap Icons
- **Database**: MySQL
- **Authentication**: Laravel Auth
- **Styling**: Custom CSS dengan gradient

## Troubleshooting

### Database Issues
Jika mengalami masalah dengan database, pastikan:
1. Database MySQL sudah berjalan
2. Kredensial database di `.env` sudah benar
3. Database sudah dibuat
4. User database memiliki permission yang cukup

### Migration Issues
Jika migration gagal:
```bash
php artisan migrate:rollback
php artisan migrate
```

### Permission Issues
Pastikan folder `storage` dan `bootstrap/cache` memiliki permission yang benar:
```bash
chmod -R 775 storage bootstrap/cache
```

## Kontribusi

1. Fork repository
2. Buat feature branch
3. Commit changes
4. Push ke branch
5. Buat Pull Request

## License

MIT License