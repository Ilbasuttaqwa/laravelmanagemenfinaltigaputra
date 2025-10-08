# 🚀 Panduan Setup Netlify dengan Neon Database

## 📋 Langkah-langkah Setup

### 1. 🔑 Setup Environment Variables di Netlify

1. **Login ke Netlify Dashboard**
   - Buka: https://app.netlify.com
   - Pilih site: `tigaputra`

2. **Masuk ke Environment Variables**
   - Site Settings → Environment Variables
   - Klik "Add variable"

3. **Tambahkan Variables Berikut:**

```bash
# Database Configuration (dari screenshot Neon Anda)
DATABASE_URL=postgresql://neondb_owner:YOUR_PASSWORD@ep-super-wind-aetmpq8t-pooler.c-2.us-east-2.aws.neon.tech/neondb?sslmode=require&channel_binding=require

DB_CONNECTION=pgsql
DB_HOST=ep-super-wind-aetmpq8t-pooler.c-2.us-east-2.aws.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=neondb_owner
DB_PASSWORD=YOUR_PASSWORD_HERE

# Laravel Configuration
APP_NAME=Laravel Management System
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_URL=https://tigaputra.netlify.app

# Logging
LOG_LEVEL=error

# Session & Cache
SESSION_DRIVER=file
CACHE_DRIVER=file
```

### 2. 🔐 Dapatkan Password Neon Database

1. **Buka Neon Console**
   - Login ke: https://console.neon.tech
   - Pilih project: `curly-star-36966813`

2. **Dapatkan Connection String**
   - Klik "Connect" di branch `main`
   - Pilih "Connection details for main"
   - Klik "Show password" untuk melihat password
   - Copy connection string lengkap

3. **Update Environment Variables**
   - Ganti `YOUR_PASSWORD_HERE` dengan password asli
   - Ganti `YOUR_APP_KEY_HERE` dengan APP_KEY yang di-generate

### 3. 🔧 Generate APP_KEY

Jalankan command ini di terminal lokal:

```bash
cd C:\laragon\www\Managemen
php artisan key:generate --show
```

Copy output `base64:...` ke environment variable `APP_KEY` di Netlify.

### 4. 🚀 Deploy ke Netlify

1. **Push ke GitHub**
   ```bash
   git add .
   git commit -m "Setup Netlify with Neon database"
   git push origin master
   ```

2. **Netlify akan otomatis deploy**
   - Netlify akan detect perubahan
   - Build process akan berjalan otomatis
   - Database migrations akan dijalankan

### 5. ✅ Verifikasi Deployment

1. **Cek Build Logs**
   - Netlify Dashboard → Deploys
   - Klik pada deploy terbaru
   - Lihat build logs untuk memastikan sukses

2. **Test Website**
   - Buka: https://tigaputra.netlify.app
   - Test login dengan akun admin
   - Verifikasi semua fitur berfungsi

## 🔧 Troubleshooting

### Database Connection Error
```bash
# Cek environment variables
# Pastikan DATABASE_URL sudah benar
# Pastikan password tidak ada spasi
```

### 404 Error
```bash
# Pastikan netlify.toml sudah ada
# Cek redirects configuration
# Pastikan public/.htaccess sudah dibuat
```

### Build Failed
```bash
# Cek Node.js version (harus 20)
# Cek PHP version (harus 8.2)
# Pastikan semua dependencies terinstall
```

## 📞 Support

Jika ada masalah:
1. Cek Netlify build logs
2. Cek Neon database connection
3. Verifikasi environment variables
4. Test database connection manual

## 🎯 Expected Result

Setelah setup selesai:
- ✅ Website accessible di https://tigaputra.netlify.app
- ✅ Database connected ke Neon
- ✅ All features working
- ✅ Admin login functional
- ✅ Employee management working
- ✅ Attendance system working
