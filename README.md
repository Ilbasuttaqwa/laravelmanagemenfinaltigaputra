# Laravel Management System

Sistem Manajemen Karyawan, Absensi, dan Pelaporan berbasis Laravel.

## Fitur Utama

- **Manajemen Karyawan**: CRUD karyawan dengan role (Karyawan/Mandor)
- **Sistem Absensi**: Pencatatan absensi harian (Full Day/Half Day)
- **Manajemen Gudang**: Pengelolaan data gudang
- **Manajemen Mandor**: Pengelolaan data mandor
- **Manajemen Kandang**: Pengelolaan data kandang
- **Manajemen Lokasi**: Pengelolaan data lokasi
- **Manajemen Pembibitan**: Pengelolaan data pembibitan
- **Laporan Bulanan**: Laporan absensi bulanan karyawan
- **Laporan Gaji**: Laporan perhitungan gaji
- **Kalender Absensi**: Visualisasi absensi dalam bentuk kalender

## Teknologi

- Laravel 10.x
- PHP 8.1+
- MySQL/SQLite
- Bootstrap 5
- Vite

## Instalasi

1. Clone repository
```bash
git clone <repository-url>
cd Managemen
```

2. Install dependencies
```bash
composer install
npm install
```

3. Setup environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Konfigurasi database di file `.env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

5. Jalankan migrasi dan seeder
```bash
php artisan migrate
php artisan db:seed
```

6. Build assets
```bash
npm run build
```

7. Jalankan server
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## User Default

Setelah menjalankan seeder, gunakan kredensial berikut untuk login:

**Admin:**
- Email: admin@tigaputra.com
- Password: password

**Manager:**
- Email: manager@tigaputra.com
- Password: password

## Struktur Database

- `users` - Data pengguna sistem
- `employees` - Data karyawan
- `absensis` - Data absensi harian
- `gudangs` - Data gudang
- `mandors` - Data mandor
- `kandangs` - Data kandang
- `lokasis` - Data lokasi
- `pembibitans` - Data pembibitan
- `monthly_attendance_reports` - Laporan absensi bulanan
- `salary_reports` - Laporan gaji

## Role & Permission

- **Manager**: Full access ke semua fitur
- **Admin**: Read-only access, tidak bisa delete data

## Development

Untuk development dengan hot reload:
```bash
npm run dev
```

## Deployment

1. Set `APP_ENV=production` di `.env`
2. Set `APP_DEBUG=false` di `.env`
3. Jalankan:
```bash
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## License

MIT License

