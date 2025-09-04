# Solusi Timeout Server Laravel

## Masalah yang Ditemukan

-   Server Laravel mengalami timeout setelah 60 detik
-   Frontend Next.js tidak bisa merespons karena backend lambat
-   Seeder database memakan waktu terlalu lama

## Solusi yang Diterapkan

### 1. Konfigurasi PHP yang Dioptimalkan

File: `php.ini`

-   Memory limit dinaikkan ke 1024M
-   Max execution time dinaikkan ke 300 detik
-   Max input time dinaikkan ke 300 detik
-   OPcache diaktifkan untuk performa yang lebih baik

### 2. Seeder yang Dioptimalkan

File: `optimized_seeder.php`

-   Batch processing untuk menghindari memory overflow
-   Query optimization dengan select specific columns
-   Disable query logging untuk performa
-   Memory management dengan garbage collection

### 3. Middleware Performance Optimization

File: `app/Http/Middleware/PerformanceOptimization.php`

-   Disable query logging secara global
-   Set memory dan execution time limits
-   Monitor slow requests
-   Add performance headers

### 4. Database Optimization

File: `optimize_database.php`

-   Test database connection performance
-   Monitor query execution time
-   Check table statistics
-   Provide optimization recommendations

### 5. Script Server yang Dioptimalkan

File: `start_optimized_server.bat`

-   Jalankan server dengan konfigurasi PHP yang dioptimalkan
-   Check database connection sebelum start server
-   Monitor performa secara real-time

## Cara Menggunakan

### 1. Jalankan Server yang Dioptimalkan

```bash
# Di direktori superadmin-backend
start_optimized_server.bat
```

### 2. Jalankan Seeder yang Dioptimalkan

```bash
# Di direktori superadmin-backend
run_seeder.bat
```

### 3. Check Database Performance

```bash
# Di direktori superadmin-backend
php -c php.ini optimize_database.php
```

## Konfigurasi yang Direkomendasikan

### Database Connection (config/database.php)

```php
'sqlsrv' => [
    'driver' => 'sqlsrv',
    'host' => env('DB_HOST', 'localhost'),
    'port' => env('DB_PORT', '1433'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8',
    'prefix' => '',
    'prefix_indexes' => true,
    'options' => [
        'ConnectionTimeout' => 60,
        'LoginTimeout' => 60,
        'QueryTimeout' => 120,  // Dinaikkan dari default
        'Encrypt' => false,
        'TrustServerCertificate' => true,
        'MultipleActiveResultSets' => false,
        'ReturnDatesAsStrings' => true,
        'ConnectionPooling' => true,
        'Pooling' => true,
    ],
],
```

### Environment Variables (.env)

```env
# Database
DB_CONNECTION=sqlsrv
DB_HOST=localhost
DB_PORT=1433
DB_DATABASE=tka_database
DB_USERNAME=sa
DB_PASSWORD=your_password

# Performance
MEMORY_LIMIT=1024M
MAX_EXECUTION_TIME=300
MAX_INPUT_TIME=300
DISABLE_QUERY_LOG=true
ENABLE_OPCACHE=true
```

## Monitoring dan Troubleshooting

### 1. Check Server Performance

-   Monitor response time di browser developer tools
-   Check Laravel logs di `storage/logs/laravel.log`
-   Monitor memory usage dengan performance headers

### 2. Database Performance

-   Jalankan `optimize_database.php` secara berkala
-   Monitor slow query log
-   Check database server resources

### 3. Common Issues

-   **Timeout masih terjadi**: Periksa database server performance
-   **Memory limit exceeded**: Naikkan memory_limit di php.ini
-   **Slow queries**: Tambahkan database indexes

## Hasil yang Diharapkan

-   Server response time < 2 detik
-   Seeder berjalan tanpa timeout
-   Frontend dapat berkomunikasi dengan backend dengan lancar
-   Memory usage yang optimal

## File yang Dibuat/Dimodifikasi

1. `php.ini` - Konfigurasi PHP yang dioptimalkan
2. `optimized_seeder.php` - Seeder yang dioptimalkan
3. `run_seeder.bat` - Script batch yang diupdate
4. `app/Http/Middleware/PerformanceOptimization.php` - Middleware performa
5. `config/performance.php` - Konfigurasi performa
6. `optimize_database.php` - Script optimasi database
7. `start_optimized_server.bat` - Script server yang dioptimalkan
8. `app/Http/Kernel.php` - Daftarkan middleware performa
