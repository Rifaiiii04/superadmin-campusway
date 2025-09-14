# Setup MSSQL Database untuk TKA SuperAdmin

## Masalah yang Ditemukan

-   Koneksi ke SQL Server gagal dengan error "Login failed for user 'sa'"
-   Ini menunjukkan masalah dengan SQL Server Authentication

## Solusi yang Perlu Dilakukan

### 1. Pastikan SQL Server Berjalan

```powershell
# Cek status SQL Server
Get-Service -Name "MSSQL*"
```

### 2. Enable SQL Server Authentication

1. Buka SQL Server Management Studio (SSMS)
2. Connect ke SQL Server instance
3. Right-click pada server name → Properties
4. Pilih Security tab
5. Pilih "SQL Server and Windows Authentication mode"
6. Restart SQL Server service

### 3. Buat User 'sa' dan Set Password

```sql
-- Di SSMS, jalankan query ini:
ALTER LOGIN sa ENABLE;
ALTER LOGIN sa WITH PASSWORD = 'tkaproject123';
```

### 4. Buat Database 'campusway_db'

```sql
-- Di SSMS, jalankan query ini:
CREATE DATABASE campusway_db;
```

### 5. Test Koneksi

Setelah setup selesai, jalankan:

```bash
php test_mssql_connection.php
```

### 6. Jalankan Migrations

```bash
php artisan migrate --force
```

### 7. Start Laravel Server

```bash
php artisan serve
```

## Alternatif: Gunakan Windows Authentication

Jika SQL Server Authentication tidak bisa diaktifkan, kita bisa menggunakan Windows Authentication dengan mengosongkan username dan password di .env file.

## Troubleshooting

-   Pastikan SQL Server listening di port 1433
-   Cek firewall settings
-   Pastikan SQL Server Browser service berjalan
-   Coba connect dengan SSMS terlebih dahulu untuk memastikan koneksi berhasil
