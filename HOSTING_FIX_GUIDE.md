# Panduan Perbaikan Laporan Gaji di Hosting

## Masalah yang Terjadi
1. **Tombol Reset tidak mengosongkan tabel** - Data tetap tampil meskipun sudah diklik Reset
2. **Lokasi dan kandang kosong** - Beberapa karyawan menampilkan "-" untuk lokasi dan kandang
3. **Data tidak konsisten** - Meskipun sudah upload file terbaru, masalah masih terjadi

## Penyebab Masalah
1. **Cache di hosting** masih menggunakan versi lama
2. **Data salary reports** sudah tersimpan dengan logika lama
3. **File belum ter-upload** dengan benar atau cache belum di-clear

## Solusi Lengkap

### Langkah 1: Upload File Terbaru
Pastikan file berikut sudah di-upload ke hosting:
- `app/Http/Controllers/SalaryReportController.php`
- `app/Console/Commands/GenerateSalaryReports.php`
- `app/Models/SalaryReport.php`

### Langkah 2: Jalankan Script Perbaikan
Upload dan jalankan script `fix_hosting_salary_reports.php` di hosting:

```bash
# Via cPanel File Manager atau SSH
php fix_hosting_salary_reports.php
```

Script ini akan:
- Clear semua cache
- Delete salary reports yang lama
- Regenerate salary reports dengan logika terbaru
- Fix masalah lokasi dan kandang yang kosong

### Langkah 3: Jika Script Gagal, Clear Cache Manual
Jika script gagal, jalankan `clear_hosting_cache.php`:

```bash
php clear_hosting_cache.php
```

### Langkah 4: Check Data
Jalankan `check_hosting_data.php` untuk melihat kondisi data:

```bash
php check_hosting_data.php
```

### Langkah 5: Test Manual
Jika masih gagal, jalankan perintah manual:

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Delete existing reports
php artisan tinker
>>> \App\Models\SalaryReport::where('tahun', 2025)->where('bulan', 10)->delete();

# Regenerate reports
php artisan salary:generate 2025 10
```

## Verifikasi Perbaikan

Setelah menjalankan perbaikan, test fitur berikut:

### 1. Test Tombol Reset
- Buka halaman laporan gaji
- Halaman awal harus kosong (tidak ada data)
- Klik filter untuk menampilkan data
- Klik Reset, tabel harus kosong lagi

### 2. Test Filter
- Pilih pembibitan "JAT-DOC1"
- Data harus tampil dengan lokasi dan kandang yang benar
- Semua karyawan dengan pembibitan yang sama harus memiliki lokasi dan kandang yang sama

### 3. Test Data Konsistensi
- Karyawan dengan pembibitan yang sama harus memiliki lokasi dan kandang yang sama
- Tidak boleh ada "-" untuk lokasi dan kandang jika pembibitan memiliki relasi yang benar

## Troubleshooting

### Jika Masih Ada Masalah

1. **Check file permissions**:
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

2. **Check database connection**:
   ```bash
   php artisan tinker
   >>> \App\Models\Employee::count();
   ```

3. **Check log errors**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Force regenerate**:
   ```bash
   php artisan salary:generate 2025 10 --force
   ```

## File yang Perlu Di-upload

Pastikan file berikut sudah di-upload dengan benar:

```
app/Http/Controllers/SalaryReportController.php
app/Console/Commands/GenerateSalaryReports.php
app/Models/SalaryReport.php
fix_hosting_salary_reports.php
clear_hosting_cache.php
check_hosting_data.php
```

## Kontak Support

Jika masih ada masalah setelah mengikuti panduan ini, berikan informasi:
1. Output dari `check_hosting_data.php`
2. Error message yang muncul
3. Screenshot halaman yang bermasalah
