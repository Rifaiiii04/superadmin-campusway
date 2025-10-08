# Manual Deploy MajorRecommendations Fix

## Langkah 1: Copy File yang Diperbaiki

```bash
# Copy controller yang sudah diperbaiki
scp app/Http/Controllers/MajorRecommendationController.php root@your-vps-ip:/var/www/superadmin/superadmin-campusway/app/Http/Controllers/

# Copy frontend component yang sudah diperbaiki
scp resources/js/Pages/SuperAdmin/MajorRecommendations.jsx root@your-vps-ip:/var/www/superadmin/superadmin-campusway/resources/js/Pages/SuperAdmin/
```

## Langkah 2: SSH ke VPS dan Build

```bash
ssh root@your-vps-ip
cd /var/www/superadmin/superadmin-campusway

# Build frontend
npm run build

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
systemctl restart apache2
systemctl restart php8.1-fpm
```

## Langkah 3: Test Halaman

Buka browser dan cek:

-   `/major-recommendations` - Harus menampilkan data jurusan
-   `/jurusan` - Harus menampilkan data jurusan

## Langkah 4: Debug

1. **Buka Developer Tools** (F12)
2. **Lihat Console tab** - Akan ada debug information
3. **Cek data structure** - Pastikan data dikirim dengan benar

## Debug Information

Setelah deploy, di browser console akan muncul:

-   `MajorRecommendations props:` - Data yang diterima dari controller
-   `MajorRecommendations data structure:` - Struktur data pagination

## Kemungkinan Masalah

1. **Data tidak dikirim** - Cek log Laravel di VPS
2. **Frontend error** - Cek browser console
3. **Build error** - Cek output npm run build
4. **Cache issue** - Clear semua cache

## Solusi

Jika masih kosong:

1. **Hard refresh browser** (Ctrl+F5)
2. **Clear browser cache**
3. **Cek log Laravel** di VPS
4. **Restart Apache dan PHP-FPM**
