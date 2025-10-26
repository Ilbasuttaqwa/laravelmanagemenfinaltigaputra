# Panduan Auto-Cleanup untuk Hosting

## Fitur Auto-Cleanup

Command auto-cleanup akan melakukan:
1. **Sync SalaryReportController logic** (hasFilter untuk tombol Reset)
2. **Cleanup data absensi lama** (default: 30 hari)
3. **Cleanup log files lama** (default: 7 hari)
4. **Cleanup cache files** (view, application, session)
5. **Cleanup salary reports lama** (default: 90 hari)

## Cara Penggunaan

### **1. Command Manual**
```bash
# Cleanup dengan default settings (30 hari untuk absensi)
php artisan auto:cleanup

# Cleanup dengan custom days
php artisan auto:cleanup --days=60

# Force cleanup (tidak peduli timestamp terakhir)
php artisan auto:cleanup --force

# Force cleanup dengan custom days
php artisan auto:cleanup --force --days=45
```

### **2. Cron Job (Otomatis)**
```bash
# Set cron job di cPanel untuk berjalan setiap hari jam 2 pagi
0 2 * * * php /path/to/auto_cleanup_cron.php
```

## Detail Cleanup

### **Data Absensi**
- **Default**: Hapus data absensi lebih dari 30 hari
- **Custom**: Bisa diatur dengan parameter `--days`
- **Safety**: Hanya hapus berdasarkan `created_at`, tidak hapus data aktif

### **Log Files**
- **Default**: Hapus log files lebih dari 7 hari
- **Safety**: Tidak menghapus `laravel.log` (log aktif)
- **Target**: File dengan ekstensi `.log` di `storage/logs/`

### **Cache Files**
- **View Cache**: Hapus semua file di `storage/framework/views/`
- **Application Cache**: Hapus semua file di `storage/framework/cache/data/`
- **Session Files**: Hapus session files lebih dari 1 hari

### **Salary Reports**
- **Default**: Hapus salary reports lebih dari 90 hari
- **Safety**: Konfirmasi jika lebih dari 100 records (kecuali `--force`)
- **Target**: Data lama yang sudah tidak diperlukan

## Keamanan

### **Backup Otomatis**
- File controller di-backup sebelum di-update
- Backup disimpan dengan timestamp: `SalaryReportController.php.backup.Y-m-d_H-i-s`

### **Error Handling**
- Semua operasi dibungkus try-catch
- Error dicatat di log file
- Script tidak berhenti jika ada error di satu bagian

### **Logging**
- Semua aktivitas cleanup dicatat di `storage/logs/laravel.log`
- Format: `Auto-cleanup: [action] [details]`

## Monitoring

### **Check Status**
```bash
# Check timestamp terakhir cleanup
php artisan tinker
>>> \Illuminate\Support\Facades\Cache::get('auto_cleanup_last_run', 0)

# Check log file
tail -f storage/logs/laravel.log | grep "Auto-cleanup"
```

### **Manual Check Data**
```bash
# Check jumlah absensi
php artisan tinker
>>> \App\Models\Absensi::count()

# Check absensi dalam 30 hari terakhir
>>> \App\Models\Absensi::where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))->count()
```

## Konfigurasi

### **Default Settings**
- **Absensi**: 30 hari
- **Logs**: 7 hari
- **Salary Reports**: 90 hari
- **Sessions**: 1 hari
- **Run Interval**: 24 jam

### **Custom Settings**
```bash
# Cleanup absensi 60 hari, logs 14 hari
php artisan auto:cleanup --days=60

# Force cleanup sekarang juga
php artisan auto:cleanup --force
```

## Troubleshooting

### **Jika Command Gagal**
1. Check log file: `storage/logs/laravel.log`
2. Check file permissions: `chmod -R 755 storage bootstrap/cache`
3. Check database connection
4. Run dengan `--force` untuk bypass timestamp check

### **Jika Cron Job Tidak Berjalan**
1. Check cron job di cPanel
2. Check file path di cron job
3. Test manual: `php auto_cleanup_cron.php`
4. Check error log di cPanel

### **Jika Data Tidak Terhapus**
1. Check `created_at` field di database
2. Check timezone settings
3. Run dengan `--force` untuk bypass safety checks

## Contoh Output

```
[2025-01-XX 02:00:01] Starting auto-cleanup...
Syncing salary reports logic...
✓ hasFilter logic already exists
Cleaning up absensi data older than 30 days...
✓ Deleted 150 old absensi records
Cleaning up log files older than 7 days...
✓ Deleted 3 old log files
Cleaning up old cache...
✓ Cleared 45 cache files
Cleaning up salary reports older than 90 days...
✓ Deleted 25 old salary reports
[2025-01-XX 02:00:15] Auto-cleanup completed successfully!
```

## Rekomendasi

### **Untuk Hosting cPanel**
1. Upload `auto_cleanup_cron.php` ke hosting
2. Set cron job: `0 2 * * * php /path/to/auto_cleanup_cron.php`
3. Monitor log file untuk memastikan berjalan dengan baik

### **Untuk Development**
1. Gunakan `php artisan auto:cleanup` secara manual
2. Test dengan `--force` untuk bypass timestamp
3. Monitor output untuk memastikan berjalan dengan benar

## File yang Perlu Di-upload

### **Minimal (Command saja)**
- `app/Console/Commands/AutoCleanupCommand.php`

### **Lengkap (Command + Cron)**
- `app/Console/Commands/AutoCleanupCommand.php`
- `auto_cleanup_cron.php`

## Kesimpulan

Auto-cleanup ini akan:
- ✅ **Otomatis sync** SalaryReportController logic
- ✅ **Otomatis hapus** data absensi lama (1 bulan)
- ✅ **Otomatis hapus** log files lama
- ✅ **Otomatis hapus** cache files
- ✅ **Otomatis hapus** salary reports lama
- ✅ **Aman** dengan backup dan error handling
- ✅ **Fleksibel** dengan parameter custom
- ✅ **Ter-monitor** dengan logging lengkap

**Dengan auto-cleanup ini, hosting akan selalu bersih dan performa optimal!** 🚀
