# Setup Instructions

## Database Setup

Karena ada konflik dengan database yang sudah ada, ikuti langkah berikut:

### 1. Buat Database Baru
```bash
# Masuk ke MySQL
mysql -u root -p

# Buat database baru
CREATE DATABASE managemen_new;

# Keluar dari MySQL
exit;
```

### 2. Update Environment File
Edit file `.env` dan ubah nama database:
```env
DB_DATABASE=managemen_new
```

### 3. Jalankan Migrasi
```bash
php artisan migrate
```

### 4. Jalankan Seeder
```bash
php artisan db:seed
```

### 5. Start Server
```bash
php artisan serve
```

## Alternative: Reset Database

Jika ingin menggunakan database yang sudah ada:

### 1. Hapus Foreign Key Constraints
```sql
-- Masuk ke MySQL dan jalankan:
USE managemen;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS pembibitan;
DROP TABLE IF EXISTS employees;
SET FOREIGN_KEY_CHECKS = 1;
```

### 2. Jalankan Migrasi
```bash
php artisan migrate
```

### 3. Jalankan Seeder
```bash
php artisan db:seed
```

## Testing

Setelah setup selesai, buka browser dan akses:
- URL: http://localhost:8000
- Login sebagai Admin: admin@company.com / password
- Login sebagai Manager: manager@company.com / password
