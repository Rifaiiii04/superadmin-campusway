# 🚀 Instruksi Setup Database MSSQL untuk TKA SuperAdmin

## Masalah Saat Ini

-   Laravel backend sudah berjalan ✅
-   Database `campusway_db` sudah ada ✅
-   User `campusway_admin` belum dibuat ❌

## Solusi: Buat User Database

### Langkah 1: Buka SQL Server Management Studio (SSMS)

1. Buka SQL Server Management Studio
2. Connect ke SQL Server instance Anda
3. Pastikan menggunakan Windows Authentication untuk login

### Langkah 2: Jalankan Script SQL

1. Buka file `create_database_user.sql` yang sudah saya buat
2. Copy semua isi file tersebut
3. Paste di SSMS dan jalankan (F5)

### Langkah 3: Verifikasi User Dibuat

Setelah menjalankan script, cek apakah user berhasil dibuat:

```sql
-- Cek login
SELECT name, type_desc, create_date, is_disabled
FROM sys.server_principals
WHERE name = 'campusway_admin';

-- Cek user di database
USE campusway_db;
SELECT name, type_desc, create_date
FROM sys.database_principals
WHERE name = 'campusway_admin';
```

### Langkah 4: Test Koneksi Laravel

Setelah user dibuat, jalankan:

```bash
php test_database_connection.php
```

### Langkah 5: Jalankan Migrations

```bash
php artisan migrate --force
```

### Langkah 6: Start Laravel Server

```bash
php artisan serve
```

## Konfigurasi Database

-   **Server**: localhost,1433
-   **Database**: campusway_db
-   **Username**: campusway_admin
-   **Password**: P@ssw0rdBaru!2025
-   **Authentication**: SQL Server Authentication

## Troubleshooting

Jika masih ada masalah:

1. Pastikan SQL Server Authentication enabled
2. Restart SQL Server service
3. Cek firewall settings
4. Pastikan SQL Server listening di port 1433

## Status Saat Ini

-   ✅ Laravel server berjalan di http://127.0.0.1:8000
-   ✅ APP_KEY sudah ter-generate
-   ✅ File .env sudah dikonfigurasi dengan benar
-   ⏳ Menunggu user database dibuat
