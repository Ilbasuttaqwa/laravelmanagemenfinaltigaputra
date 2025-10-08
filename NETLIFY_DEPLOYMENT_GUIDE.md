# 🚀 PANDUAN DEPLOYMENT NETLIFY + NEON DATABASE

## ✅ STATUS SAAT INI
- ✅ Database Neon sudah terhubung di Netlify
- ✅ Environment variables NETLIFY_DATABASE_URL tersedia
- ✅ Konfigurasi Laravel sudah diupdate untuk PostgreSQL
- ✅ File netlify.toml sudah dibuat

## 🎯 LANGKAH DEPLOYMENT KE NETLIFY

### **STEP 1: Set Environment Variables di Netlify**

Masuk ke Netlify Dashboard → Project Settings → Environment Variables:

```
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:0TjCf2l05M8JK71JqyWys8Y/4NlALzCS3hQxwIIitHs=
APP_URL=https://tigaputra.netlify.app
DB_CONNECTION=pgsql
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

**PENTING:** `NETLIFY_DATABASE_URL` sudah otomatis tersedia dari Neon extension.

### **STEP 2: Update netlify.toml**

File `netlify.toml` sudah dibuat dengan konfigurasi:
- Build command yang mencakup migration dan seeding
- Redirect rules untuk Laravel
- Cache headers untuk assets

### **STEP 3: Deploy ke Netlify**

1. **Connect Repository:**
   - Netlify Dashboard → "New site from Git"
   - Pilih GitHub repository "Ilbasuttaqwa/tigaputralaravel"
   - Set build settings:
     - Build command: `composer install --no-dev --optimize-autoloader && npm install && npm run build && php artisan migrate --force && php artisan db:seed --force && php artisan config:cache && php artisan route:cache && php artisan view:cache`
     - Publish directory: `public`

2. **Set Environment Variables:**
   - Gunakan variables dari `netlify-vars.txt`

### **STEP 4: Fix Download/Blank Issue**

Masalah download/blank biasanya karena:

1. **Wrong redirect rules** - ✅ Fixed dengan netlify.toml
2. **Missing index.php** - ✅ Fixed dengan redirect rules
3. **Wrong build output** - ✅ Fixed dengan publish = "public"

## 🔧 TROUBLESHOOTING

### **Jika masih download/blank:**

1. **Cek Build Logs:**
   - Netlify Dashboard → Deploys → Click latest deploy
   - Lihat build logs untuk error

2. **Cek Function Logs:**
   - Netlify Dashboard → Functions → View logs

3. **Cek Database Connection:**
   ```php
   // Test di tinker
   php artisan tinker
   DB::connection()->getPdo();
   ```

### **Jika Database Error:**

1. **Cek NETLIFY_DATABASE_URL:**
   - Pastikan environment variable tersedia
   - Test connection di Netlify dashboard

2. **Run Migration Manual:**
   ```bash
   php artisan migrate:fresh --seed --force
   ```

## 📊 EXPECTED BUILD PROCESS

```
1. Installing dependencies...
2. Running "composer install --no-dev --optimize-autoloader"
3. Running "npm install"
4. Running "npm run build"
5. Running "php artisan migrate --force"
6. Running "php artisan db:seed --force"
7. Running "php artisan config:cache"
8. Running "php artisan route:cache"
9. Running "php artisan view:cache"
10. Deployment successful!
```

## 🔐 LOGIN CREDENTIALS

Setelah database ter-seed, gunakan:

### **Admin Account:**
- **Email:** `admin@tigaputra.com`
- **Password:** `password`

### **Manager Account:**
- **Email:** `manager@tigaputra.com`
- **Password:** `password`

### **Test Account:**
- **Email:** `test@test.com`
- **Password:** `123456`

## 🎉 SETELAH DEPLOYMENT BERHASIL

### **Yang Akan Terjadi:**
- ✅ Laravel berjalan di Netlify Functions
- ✅ Database PostgreSQL (Neon) terhubung
- ✅ User ter-seed otomatis
- ✅ Static assets di-cache dengan baik
- ✅ Redirect rules bekerja dengan benar

### **URL Aplikasi:**
- **Netlify:** `https://tigaputra.netlify.app`
- **Custom Domain:** (jika sudah setup)

## 📞 SUPPORT

Jika masih ada masalah:
1. Cek build logs di Netlify dashboard
2. Cek function logs untuk error details
3. Pastikan NETLIFY_DATABASE_URL tersedia
4. Test database connection

---

**🎯 READY FOR NETLIFY DEPLOYMENT!** 

Database Neon sudah terhubung, tinggal deploy ke Netlify! 🚀
