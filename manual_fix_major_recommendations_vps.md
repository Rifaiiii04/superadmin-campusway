# Manual Fix MajorRecommendations Filter Error di VPS

## Langkah 1: Copy File yang Diperbaiki

```bash
# Copy controller yang sudah diperbaiki
scp app/Http/Controllers/MajorRecommendationController.php root@your-vps-ip:/var/www/superadmin/superadmin-campusway/app/Http/Controllers/

# Copy frontend component yang sudah diperbaiki
scp resources/js/Pages/SuperAdmin/MajorRecommendations.jsx root@your-vps-ip:/var/www/superadmin/superadmin-campusway/resources/js/Pages/SuperAdmin/

# Copy debug script
scp test_vps_major_recommendations_debug.php root@your-vps-ip:/var/www/superadmin/superadmin-campusway/
```

## Langkah 2: SSH ke VPS dan Test

```bash
ssh root@your-vps-ip
cd /var/www/superadmin/superadmin-campusway

# Test data structure
php test_vps_major_recommendations_debug.php
```

## Langkah 3: Build dan Restart

```bash
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

## Langkah 4: Test Halaman

Buka browser dan cek:

-   `/major-recommendations` - Harus menampilkan data jurusan tanpa filter error
-   `/jurusan` - Harus menampilkan data jurusan tanpa filter error

## Debugging

Jika masih ada error `p.filter is not a function`:

1. **Cek Data Structure:**

    ```bash
    php test_vps_major_recommendations_debug.php
    ```

2. **Cek Log Laravel:**

    ```bash
    tail -f storage/logs/laravel.log
    ```

3. **Test Controller langsung:**

    ```bash
    php artisan tinker
    >>> $majors = App\Models\MajorRecommendation::paginate(10);
    >>> $majors->data
    >>> $majors->total
    ```

4. **Cek Browser Console:**
    - Buka Developer Tools
    - Lihat Console tab
    - Cek error `p.filter is not a function`

## Kemungkinan Masalah

1. **Data tidak dikirim dengan benar** - Controller tidak mengirim data pagination
2. **Frontend component error** - Ada masalah dengan data handling di React
3. **Build error** - Frontend tidak ter-build dengan benar
4. **Cache issue** - Cache lama masih digunakan

## Solusi

Jika masih error, coba:

1. **Hard refresh browser** (Ctrl+F5)
2. **Clear browser cache**
3. **Restart Apache dan PHP-FPM**
4. **Check file permissions**
