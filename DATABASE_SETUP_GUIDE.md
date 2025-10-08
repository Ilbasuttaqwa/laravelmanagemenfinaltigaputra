# 🗄️ PANDUAN SETUP DATABASE NETLIFY + NEON

## ❌ MASALAH YANG TERJADI

Build Netlify gagal karena:
```
could not find driver (Connection: pgsql, SQL: select * from information_schema.tables...)
```

**Penyebab:** Netlify build environment tidak memiliki PostgreSQL driver.

## ✅ SOLUSI: BUILD TANPA DATABASE MIGRATION

### **STEP 1: Update netlify.toml**

Gunakan konfigurasi yang lebih sederhana:

```toml
[build]
  command = "composer install --no-dev --optimize-autoloader && npm install && npm run build"
  publish = "public"

[build.environment]
  PHP_VERSION = "8.2"
  NODE_VERSION = "18"

[[redirects]]
  from = "/*"
  to = "/index.php"
  status = 200
```

### **STEP 2: Set Environment Variables di Netlify**

```
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:0TjCf2l05M8JK71JqyWys8Y/4NlALzCS3hQxwIIitHs=
APP_URL=https://tigaputra.netlify.app
DB_CONNECTION=pgsql
DATABASE_URL=${NETLIFY_DATABASE_URL}
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### **STEP 3: Setup Database Setelah Deployment**

Setelah deployment berhasil, jalankan database setup:

#### **Option A: Via Netlify Functions (Recommended)**

1. **Buat Function untuk Database Setup:**
   ```php
   // netlify/functions/setup-db.php
   <?php
   require_once __DIR__ . '/../../vendor/autoload.php';
   
   $app = require_once __DIR__ . '/../../bootstrap/app.php';
   
   try {
       Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
       return [
           'statusCode' => 200,
           'body' => json_encode(['message' => 'Database setup completed!'])
       ];
   } catch (Exception $e) {
       return [
           'statusCode' => 500,
           'body' => json_encode(['error' => $e->getMessage()])
       ];
   }
   ```

2. **Akses Function:**
   ```
   https://tigaputra.netlify.app/.netlify/functions/setup-db
   ```

#### **Option B: Via SSH/Terminal**

1. **SSH ke Netlify (jika tersedia)**
2. **Jalankan commands:**
   ```bash
   php artisan migrate:fresh --seed --force
   ```

#### **Option C: Via Laravel Tinker (Manual)**

1. **Buat route temporary:**
   ```php
   // routes/web.php
   Route::get('/setup-db', function() {
       try {
           Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
           return 'Database setup completed!';
       } catch (Exception $e) {
           return 'Error: ' . $e->getMessage();
       }
   });
   ```

2. **Akses route:**
   ```
   https://tigaputra.netlify.app/setup-db
   ```

### **STEP 4: Verify Database Connection**

Setelah setup, test database connection:

```php
// Test di tinker atau route
DB::connection()->getPdo();
```

## 🔐 LOGIN CREDENTIALS

Setelah database ter-setup, gunakan:

- **Admin:** `admin@tigaputra.com` / `password`
- **Manager:** `manager@tigaputra.com` / `password`
- **Test:** `test@test.com` / `123456`

## 🎯 WORKFLOW YANG BENAR

1. ✅ **Deploy ke Netlify** (tanpa database migration)
2. ✅ **Set environment variables**
3. ✅ **Setup database** (via function/route)
4. ✅ **Test login**
5. ✅ **Remove setup route** (untuk security)

## 🚀 DEPLOYMENT STEPS

1. **Update netlify.toml:**
   ```bash
   git add netlify.toml
   git commit -m "fix: Remove database migration from build command"
   git push origin master
   ```

2. **Redeploy di Netlify**

3. **Setup database** setelah deployment berhasil

4. **Test aplikasi**

---

**🎯 SOLUSI INI AKAN MENGATASI BUILD ERROR!** 

Database akan di-setup setelah deployment berhasil. 🚀
