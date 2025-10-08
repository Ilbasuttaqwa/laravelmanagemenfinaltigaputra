# Fix MySQL Crash di Railway

## Masalah yang Terjadi:
MySQL service crash dengan error "Database is uninitialized and password option is not specified"

## Solusi:

### 1. Hapus MySQL Service yang Crash
- Di Railway dashboard, delete MySQL service yang crash
- Restart project

### 2. Add MySQL Service Baru
- Click "New Service" → "Database" → "MySQL"
- Railway akan auto-generate variables

### 3. Manual Environment Variables
Jika masih crash, set manual di Variables:

```
MYSQL_ROOT_PASSWORD=railway123
MYSQL_DATABASE=managemen
MYSQL_USER=railway
MYSQL_PASSWORD=railway123
```

### 4. Alternative: Gunakan PostgreSQL
Jika MySQL masih bermasalah, switch ke PostgreSQL:
- Add "PostgreSQL" service instead of MySQL
- Update DB_CONNECTION=pgsql
- Railway PostgreSQL lebih stable

### 5. Check Logs
- Di Railway dashboard → MySQL service → Logs
- Lihat error message yang detail
- Restart service jika perlu

## Quick Fix Commands:
```bash
# Restart MySQL service
railway service restart mysql

# Check service status
railway service status

# View logs
railway logs mysql
```
