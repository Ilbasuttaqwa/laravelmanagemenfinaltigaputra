# 🚂 Railway Setup Guide - Step by Step

## 📋 **Langkah-langkah Setup Railway:**

### **Step 1: Login ke Railway**
```bash
railway login
```
- Browser akan terbuka untuk login
- Login dengan akun Railway Anda
- Setelah login berhasil, kembali ke terminal

### **Step 2: Jalankan Setup Complete**
```bash
./railway-setup-complete.sh
```

### **Step 3: Atau Manual Setup**

#### **A. Set Environment Variables:**
```bash
railway variables set APP_NAME="Laravel Management System"
railway variables set APP_ENV=production
railway variables set APP_DEBUG=false
railway variables set APP_URL=https://zealous-friendship-production.up.railway.app
railway variables set DB_CONNECTION=mysql
railway variables set DB_HOST=mysql.railway.internal
railway variables set DB_PORT=3306
railway variables set DB_DATABASE=railway
railway variables set DB_USERNAME=root
railway variables set DB_PASSWORD=qhtQVisSzaRiUtbNsGSczxiikubsyrAj
railway variables set PORT=8080
railway variables set SESSION_DRIVER=file
railway variables set CACHE_DRIVER=file
```

#### **B. Generate APP_KEY:**
```bash
railway run php artisan key:generate --show
```
Copy hasilnya dan set:
```bash
railway variables set APP_KEY="base64:YOUR_GENERATED_KEY_HERE"
```

#### **C. Setup Database:**
```bash
railway run php artisan migrate --force
railway run php artisan db:seed --force
```

#### **D. Restart Service:**
```bash
railway service restart
```

## 🎯 **Setelah Setup Complete:**

### **URL Aplikasi:**
```
https://zealous-friendship-production.up.railway.app
```

### **Default Login:**
```
Manager: manager@example.com / password
Admin: admin@example.com / password
```

### **Features yang Tersedia:**
- ✅ Employee Management (Gudang, Mandor)
- ✅ Attendance System (Daily & Calendar)
- ✅ Monthly Reports
- ✅ Salary Reports
- ✅ PDF Export

## 🔍 **Troubleshooting:**

### **Jika Error 404:**
1. Check environment variables sudah ter-set
2. Check APP_KEY sudah di-generate
3. Check database migrations sudah di-run
4. Restart service

### **Jika Database Error:**
1. Check MySQL service running
2. Check database credentials
3. Run migrations lagi

### **Jika Login Error:**
1. Check database seeding
2. Check user seeder
3. Run seeder lagi

## 📞 **Support:**
Jika ada masalah, check:
1. Railway dashboard logs
2. Laravel service logs
3. MySQL service logs
