# Panduan Auto-Cleanup Lengkap

## File Auto-Cleanup yang Sudah Dibuat

### **1. Command Auto-Cleanup**
✅ `app/Console/Commands/AutoCleanupCommand.php` - Command lengkap untuk cleanup

### **2. Script Cron untuk Hosting**
✅ `auto_cleanup_cron.php` - Script cron untuk hosting

## Fitur Auto-Cleanup

### **1. Sync Salary Reports Logic**
- ✅ **Check hasFilter logic** di SalaryReportController
- ✅ **Auto-update controller** jika logic belum ada
- ✅ **Backup file** sebelum update
- ✅ **Logging** untuk monitoring

### **2. Cleanup Data Lama**
- ✅ **Absensi data** (default: 30 hari)
- ✅ **Log files** (default: 7 hari)
- ✅ **Cache files** (semua cache lama)
- ✅ **Session files** (1 hari)
- ✅ **Salary reports** (default: 90 hari)

### **3. Smart Cleanup**
- ✅ **Timestamp check** - hanya cleanup jika diperlukan
- ✅ **Force option** - bypass timestamp check
- ✅ **Configurable days** - custom berapa hari data disimpan
- ✅ **Confirmation** untuk data besar
- ✅ **Error handling** yang robust

## Cara Penggunaan

### **1. Command Manual (Lokal/Hosting)**
```bash
# Cleanup dengan default settings (30 hari)
php artisan auto:cleanup

# Force cleanup (bypass timestamp check)
php artisan auto:cleanup --force

# Custom days (misal: 7 hari)
php artisan auto:cleanup --days=7

# Force cleanup dengan custom days
php artisan auto:cleanup --force --days=15
```

### **2. Script Hosting (Cron Job)**
```bash
# Manual
php auto_cleanup_cron.php

# Cron job (setiap hari jam 2 pagi)
0 2 * * * php /path/to/auto_cleanup_cron.php

# Cron job (setiap minggu)
0 2 * * 0 php /path/to/auto_cleanup_cron.php
```

## Implementasi di Hosting

### **Langkah 1: Upload File**
- `app/Console/Commands/AutoCleanupCommand.php`
- `auto_cleanup_cron.php`

### **Langkah 2: Set Cron Job**
1. **Login ke cPanel**
2. **Cari "Cron Jobs"** (biasanya di bagian "Advanced")
3. **Tambahkan cron job baru:**
   ```
   Minute: 0
   Hour: 2
   Day: *
   Month: *
   Weekday: *
   Command: php /home/yourcpaneluser/public_html/auto_cleanup_cron.php
   ```
4. **Save cron job**

### **Langkah 3: Test Manual**
```bash
# Test script
php auto_cleanup_cron.php

# Test command
php artisan auto:cleanup --force
```

## Monitoring

### **Check Logs**
```bash
# Check auto-cleanup logs
tail -f storage/logs/laravel.log | grep "Auto-cleanup"

# Check last cleanup time
php artisan tinker
>>> \Illuminate\Support\Facades\Cache::get('auto_cleanup_last_run', 0)
```

### **Check Data**
```bash
# Check absensi count
php artisan tinker
>>> \App\Models\Absensi::count()

# Check salary reports count
>>> \App\Models\SalaryReport::count()

# Check log files
ls -la storage/logs/
```

## Konfigurasi

### **Default Settings**
- **Absensi data**: 30 hari
- **Log files**: 7 hari
- **Cache files**: Semua cache lama
- **Session files**: 1 hari
- **Salary reports**: 90 hari
- **Cleanup interval**: 24 jam

### **Custom Settings**
```bash
# Cleanup absensi data 7 hari
php artisan auto:cleanup --days=7

# Cleanup absensi data 60 hari
php artisan auto:cleanup --days=60
```

## Troubleshooting

### **Jika Auto-Cleanup Tidak Berjalan**
1. **Check cron job**:
   ```bash
   crontab -l
   ```

2. **Check file permissions**:
   ```bash
   chmod +x auto_cleanup_cron.php
   ```

3. **Check PHP path**:
   ```bash
   which php
   ```

4. **Test manual**:
   ```bash
   php auto_cleanup_cron.php
   ```

### **Jika Data Terhapus Terlalu Banyak**
1. **Check timestamp**:
   ```bash
   php artisan tinker
   >>> \Illuminate\Support\Facades\Cache::get('auto_cleanup_last_run', 0)
   ```

2. **Check backup files**:
   ```bash
   ls -la app/Http/Controllers/SalaryReportController.php.backup.*
   ```

3. **Restore dari backup**:
   ```bash
   cp app/Http/Controllers/SalaryReportController.php.backup.2025-01-27_14-30-00 app/Http/Controllers/SalaryReportController.php
   ```

## Keamanan

### **Backup Strategy**
- ✅ **Auto-backup** sebelum update controller
- ✅ **Timestamp backup** untuk tracking
- ✅ **Logging** untuk audit trail
- ✅ **Confirmation** untuk data besar

### **Data Protection**
- ✅ **Selective cleanup** - hanya data lama
- ✅ **Configurable retention** - custom berapa hari
- ✅ **Force option** - manual control
- ✅ **Error handling** - rollback jika gagal

## Performance Impact

### **Before Auto-Cleanup**
- ❌ **Database size**: Bertambah terus
- ❌ **Log files**: Menumpuk
- ❌ **Cache files**: Tidak ter-clear
- ❌ **Performance**: Menurun seiring waktu

### **After Auto-Cleanup**
- ✅ **Database size**: Terkontrol
- ✅ **Log files**: Ter-cleanup otomatis
- ✅ **Cache files**: Fresh selalu
- ✅ **Performance**: Optimal terus

## Kesimpulan

Dengan auto-cleanup ini:
- ✅ **Data lama ter-cleanup** otomatis
- ✅ **Performance optimal** terus
- ✅ **Storage space** terkontrol
- ✅ **Maintenance** minimal
- ✅ **Monitoring** lengkap

**SISTEM SIAP PRODUCTION dengan auto-cleanup yang aman dan otomatis!** 🚀

## File yang Perlu Di-upload

### **Minimal (Auto-Cleanup Basic)**
- `app/Console/Commands/AutoCleanupCommand.php`
- `auto_cleanup_cron.php`

### **Lengkap (Dengan Performance Optimization)**
- `app/Console/Commands/AutoCleanupCommand.php`
- `auto_cleanup_cron.php`
- `app/Http/Middleware/PerformanceOptimizationMiddleware.php`
- `app/Services/SmartCacheService.php`
- `app/Http/Controllers/OptimizedAbsensiController.php`
- `database/migrations/2025_01_27_000000_add_performance_indexes.php`

**Auto-cleanup sudah siap dan ter-test!** 💪
