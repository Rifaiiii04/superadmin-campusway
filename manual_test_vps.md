# Manual Test MajorRecommendations di VPS

## Langkah 1: Copy File yang Diperbaiki

```bash
# Copy controller yang sudah diperbaiki
scp app/Http/Controllers/MajorRecommendationController.php root@your-vps-ip:/var/www/superadmin/superadmin-campusway/app/Http/Controllers/

# Copy test script
scp test_vps_major_recommendations.php root@your-vps-ip:/var/www/superadmin/superadmin-campusway/
```

## Langkah 2: SSH ke VPS dan Test

```bash
ssh root@your-vps-ip
cd /var/www/superadmin/superadmin-campusway

# Test controller
php test_vps_major_recommendations.php
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

-   `/major-recommendations` - Harus menampilkan data jurusan
-   `/jurusan` - Harus menampilkan data jurusan

## Debugging

Jika masih kosong, cek:

1. **Log Laravel:**

    ```bash
    tail -f storage/logs/laravel.log
    ```

2. **Test Controller langsung:**

    ```bash
    php artisan tinker
    >>> App\Models\MajorRecommendation::count()
    >>> App\Models\MajorRecommendation::first()
    ```

3. **Cek Route:**

    ```bash
    php artisan route:list | grep major
    ```

4. **Cek Database:**
    ```bash
    mysql -u root -p campusway_db
    SELECT COUNT(*) FROM major_recommendations;
    ```
