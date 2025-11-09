# 🔧 Quick Fix Guide - Troubleshooting Common Issues

## 🚨 MASALAH YANG BARU SAJA DIPERBAIKI

### 1. Gaji Tidak Tampil Setelah Reload
**Penyebab**: Vite config men-drop console.log → error tidak terlihat
**Fix**: Console logging di-enable kembali untuk debugging

### 2. Tombol Delete Tidak Berfungsi
**Penyebab**: Bootstrap Modal conflicts, inconsistent delete handlers
**Fix**: Universal delete handler dengan SweetAlert2

### 3. Notifikasi Tidak Muncul
**Penyebab**: Tidak ada notification system yang konsisten
**Fix**: Global notification functions (showSuccess, showError, showWarning)

---

## 🛠️ CARA MEMPERBAIKI DI LOCAL/PRODUCTION

### Step 1: Pull Latest Code
```bash
git pull origin claude/pas-tambah-011CUxNTcqcRLzkBNcPDaWbY
```

### Step 2: Install & Build
```bash
# Clear old build
rm -rf node_modules package-lock.json public/build

# Install dependencies
npm install

# Build for production
npm run build

# Check build output
ls -la public/build/
```

**Expected files**:
- ✅ `manifest.json`
- ✅ `assets/app-[hash].js`
- ✅ `assets/delete-handler-[hash].js`
- ✅ `assets/cache-[hash].js`

### Step 3: Laravel Cache Clear
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Step 4: Test
1. **Test Gaji Auto-Fill**:
   - Buka: Tambah Absensi
   - Pilih karyawan
   - Pilih status
   - **Check Console** (F12 → Console)
   - Should see: "🚀 Absensi form script loaded - Version 3.1"
   - Should see: "📊 RINGKASAN PERHITUNGAN"

2. **Test Delete Button**:
   - Buka: Master Lokasi
   - Klik tombol Hapus (trash icon)
   - Should see: SweetAlert2 confirmation dialog
   - **Check Console**: Should see "🗑️ Delete requested"

3. **Test Notifications**:
   - After delete success → Beautiful success notification
   - After delete error → Clear error message

---

## 🔍 DEBUGGING JIKA MASIH BERMASALAH

### Problem: Gaji Tidak Tampil

**Step 1: Buka Console Browser (F12 → Console)**

Look for these messages:
```
✅ GOOD:
🚀 Absensi form script loaded - Version 3.1
✅ Elements found: {...}
👥 Total employees in dropdown: X

❌ BAD (ada error):
TypeError: cannot read property...
ReferenceError: ... is not defined
```

**Step 2: Check Employee Data Attribute**

In Console, type:
```javascript
// Cek dropdown karyawan
const select = document.getElementById('employee_id');
console.log('Options:', select.options.length);

// Cek data attribute
const opt = select.options[1]; // First employee
console.log('Gaji:', opt.getAttribute('data-gaji'));
console.log('Source:', opt.getAttribute('data-source'));
```

Expected output:
```
Options: 15
Gaji: "3000000"
Source: "employee"
```

**Step 3: Manually Trigger Calculation**

```javascript
// Get elements
const employeeSelect = document.getElementById('employee_id');
const statusFull = document.querySelector('input[name="status"][value="full"]');

// Select first employee
employeeSelect.selectedIndex = 1;
employeeSelect.dispatchEvent(new Event('change'));

// Select full day
statusFull.checked = true;
statusFull.dispatchEvent(new Event('change'));

// Check if gaji filled
console.log('Gaji Pokok:', document.getElementById('gaji_pokok_saat_itu').value);
console.log('Gaji Dibayar:', document.getElementById('gaji_hari_itu').value);
```

---

### Problem: Delete Button Tidak Berfungsi

**Step 1: Check SweetAlert2 Loaded**

In Console:
```javascript
typeof Swal
```

Expected: `"function"`
If `"undefined"` → SweetAlert2 not loaded, check internet connection

**Step 2: Check Delete Handler Loaded**

```javascript
typeof confirmDelete
```

Expected: `"function"`
If `"undefined"` → delete-handler.js not compiled

**Step 3: Manual Delete Test**

```javascript
// Create fake button with data
const btn = document.createElement('button');
btn.setAttribute('data-id', '1');
btn.setAttribute('data-name', 'Test Item');
btn.setAttribute('data-url', window.location.origin + '/manager/lokasis/1');

// Try to call confirmDelete
confirmDelete(btn);
```

Should show SweetAlert2 dialog.

---

### Problem: Build Gagal / npm run build Error

**Error**: `ENOENT: no such file or directory`

**Fix**:
```bash
rm -rf node_modules package-lock.json
npm install
npm run build
```

**Error**: `Cannot find module 'delete-handler.js'`

**Fix**: Check file exists
```bash
ls -la resources/js/delete-handler.js
```

If missing, pull from git again.

---

## 📝 EXPECTED CONSOLE OUTPUT (SUKSES)

### Saat Load Halaman Tambah Absensi:
```
🚀 Absensi form script loaded - Version 3.1 (With Smart Rounding)
📅 Current date: 2025-01-09T...
✨ NEW: Gaji pembulatan otomatis untuk kemudahan pembayaran
✅ Elements found: {employeeSelect: true, ...}
👥 Total employees in dropdown: 15
📋 Employee options: [...]
```

### Saat Pilih Karyawan & Status:
```
💰 Gaji pokok: Rp 200,000,000
📝 Selected status: full
✅ Full day: Rp 200,000,000 / 30 hari = Rp 6,666,667
🔢 Pembulatan ke Rp 10,000 : Rp 6,666,667 → Rp 6,670,000
✅ Gaji yang dibayarkan (final): Rp 6,670,000
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📊 RINGKASAN PERHITUNGAN:
   Gaji Pokok/Bulan : Rp 200,000,000
   Status           : Full Day
   Gaji Perhitungan : Rp 6,666,667
   Pembulatan       : Rp 10,000
   💰 GAJI DIBAYAR  : Rp 6,670,000
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### Saat Klik Delete:
```
🗑️ Delete requested: {itemId: "1", itemName: "Lokasi A", ...}
Delete response status: 200
Delete response data: {success: true, ...}
```

---

## 🎯 CHECKLIST SETELAH FIX

After deploying fixes, verify:

- [ ] `npm run build` berhasil tanpa error
- [ ] File `public/build/manifest.json` exists
- [ ] File `public/build/assets/delete-handler-*.js` exists
- [ ] Browser Console tidak ada error merah
- [ ] Gaji auto-fill works (pilih karyawan → gaji muncul)
- [ ] Pembulatan works (ubah opsi → gaji final berubah)
- [ ] Delete button shows SweetAlert2 dialog
- [ ] Success notification after delete
- [ ] Page reload atau DataTable reload after delete

---

## 🆘 JIKA TETAP TIDAK BERFUNGSI

1. **Clear Everything**:
```bash
# Clear node_modules
rm -rf node_modules package-lock.json

# Clear Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Clear public build
rm -rf public/build

# Reinstall & rebuild
npm install
npm run build

# Clear browser cache
# Press: Ctrl+Shift+Delete (Chrome/Firefox)
# Check: Cached images and files
# Clear data
```

2. **Check Permissions**:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/build
```

3. **Check .env**:
```bash
APP_ENV=production
APP_DEBUG=false  # atau true untuk debugging
VITE_APP_NAME="${APP_NAME}"
```

4. **Restart Services**:
```bash
# Apache
sudo service apache2 restart

# Nginx
sudo service nginx restart

# PHP-FPM
sudo service php8.1-fpm restart
```

---

## 📞 CONTACT SUPPORT

Jika semua langkah di atas sudah dicoba tapi masih bermasalah:

1. Screenshot browser console (F12 → Console)
2. Screenshot Network tab (F12 → Network) saat error terjadi
3. Copy paste error message dari console
4. Kasih tahu step yang sudah dicoba

**Remember**: Console adalah teman terbaik untuk debugging! 🐛

---

**Last Updated**: 2025-01-09
**Version**: 3.1 - With Delete Handler Fix
