# Troubleshooting SQL Server Timeout Error

## Masalah

Error yang terjadi:

```
SQLSTATE[08001]: [Microsoft][ODBC Driver 17 for SQL Server]TCP Provider: Timeout error [258]
```

## Solusi yang Sudah Diterapkan

### 1. Peningkatan Timeout Settings

File `config/database.php` telah diupdate dengan konfigurasi timeout yang lebih panjang:

```php
'options' => [
    'ConnectionTimeout' => 30,      // Dinaikkan dari 10
    'LoginTimeout' => 30,           // Dinaikkan dari 10
    'QueryTimeout' => 60,           // Dinaikkan dari 15
    'RetryCount' => 3,              // Ditambahkan retry
    'RetryInterval' => 1000,        // Interval retry 1 detik
    // ... konfigurasi lainnya
]
```

### 2. Optimasi Connection Pooling

-   `MinPoolSize`: 1 (dikurangi dari 2)
-   `MaxPoolSize`: 10 (dikurangi dari 20)
-   `PoolBlockingPeriod`: 5000 (dinaikkan dari 2000)

## Langkah Troubleshooting

### 1. Periksa Koneksi Database

Jalankan script test koneksi:

```bash
php test_connection.php
```

### 2. Periksa Konfigurasi Environment

Pastikan file `.env` memiliki konfigurasi yang benar:

```env
DB_CONNECTION=sqlsrv
DB_HOST=localhost
DB_PORT=1433
DB_DATABASE=nama_database_anda
DB_USERNAME=username_anda
DB_PASSWORD=password_anda
```

### 3. Periksa SQL Server

-   Pastikan SQL Server sedang berjalan
-   Periksa apakah port 1433 terbuka
-   Pastikan SQL Server Browser service berjalan

### 4. Periksa Network

-   Test koneksi dengan `telnet localhost 1433`
-   Periksa firewall settings
-   Pastikan tidak ada proxy yang memblokir koneksi

### 5. Periksa ODBC Driver

-   Pastikan ODBC Driver 17 for SQL Server terinstall
-   Update driver jika diperlukan

## Konfigurasi Tambahan

### Untuk Development

Jika masih mengalami timeout, tambahkan konfigurasi berikut di `.env`:

```env
DB_CONNECTION_TIMEOUT=60
DB_LOGIN_TIMEOUT=60
DB_QUERY_TIMEOUT=120
```

### Untuk Production

Gunakan konfigurasi yang lebih konservatif:

```php
'options' => [
    'ConnectionTimeout' => 15,
    'LoginTimeout' => 15,
    'QueryTimeout' => 30,
    'ConnectionPooling' => true,
    'Pooling' => true,
    'MinPoolSize' => 2,
    'MaxPoolSize' => 5,
]
```

## Monitoring dan Logging

### Enable Query Logging

Tambahkan di `config/database.php`:

```php
'logging' => true,
'log_queries' => true,
```

### Monitor Performance

Gunakan tools seperti:

-   SQL Server Profiler
-   Laravel Telescope (untuk development)
-   Application Performance Monitoring (APM)

## Kontak Support

Jika masalah masih berlanjut, hubungi tim development dengan informasi:

1. Error message lengkap
2. Konfigurasi database yang digunakan
3. Log file dari `storage/logs/`
4. Hasil dari `php test_connection.php`
