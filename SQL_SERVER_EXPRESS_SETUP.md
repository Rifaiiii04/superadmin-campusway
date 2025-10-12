# SQL Server Express Setup Guide

## Masalah yang Ditemukan

Error yang terjadi:

```
SQLSTATE[08001]: [Microsoft][ODBC Driver 17 for SQL Server]TCP Provider: Timeout error [258]
```

**Root Cause**: SQL Server Express service tidak berjalan.

## Solusi

### 1. Start SQL Server Express Service

**Opsi A: Menggunakan Script Batch (Recommended)**

1. Buka Command Prompt sebagai Administrator
2. Navigate ke folder `superadmin-backend`
3. Jalankan: `start_sqlserver_express.bat`

**Opsi B: Manual**

1. Buka Services.msc
2. Cari "SQL Server (SQLEXPRESS)"
3. Right-click → Start

**Opsi C: PowerShell (sebagai Administrator)**

```powershell
Start-Service -Name "MSSQL`$SQLEXPRESS"
```

### 2. Verifikasi Service Berjalan

Jalankan command berikut untuk memastikan service berjalan:

```powershell
Get-Service -Name "*SQL*" | Select-Object Name, Status
```

Pastikan status "SQL Server (SQLEXPRESS)" adalah "Running".

### 3. Test Koneksi Database

Setelah service berjalan, jalankan:

```bash
php test_sqlserver_express.php
```

### 4. Konfigurasi .env

Buat file `.env` dengan konfigurasi berikut:

```env
APP_NAME=TKA_Web_Application
APP_ENV=local
APP_KEY=base64:your_app_key_here
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Database Configuration for SQL Server Express
DB_CONNECTION=sqlsrv
DB_HOST=localhost\\SQLEXPRESS
DB_DATABASE=campusway_db
DB_USERNAME=campusway_admin
DB_PASSWORD=P@ssw0rdBaru!2025

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# Database timeout settings
DB_CONNECTION_TIMEOUT=60
DB_LOGIN_TIMEOUT=60
DB_QUERY_TIMEOUT=120
```

### 5. Jalankan Laravel Server

Setelah semua konfigurasi selesai:

```bash
php artisan serve
```

## Troubleshooting

### Jika SQL Server Express tidak bisa di-start:

1. **Periksa Windows Event Log**

    - Buka Event Viewer
    - Cari error di Windows Logs → Application

2. **Periksa SQL Server Configuration Manager**

    - Buka SQL Server Configuration Manager
    - Pastikan SQL Server Services → SQL Server (SQLEXPRESS) status "Running"

3. **Periksa TCP/IP Protocol**

    - SQL Server Network Configuration → Protocols for SQLEXPRESS
    - Pastikan TCP/IP Enabled
    - Pastikan port 1433 atau dynamic port dikonfigurasi

4. **Restart Windows**
    - Kadang restart Windows diperlukan untuk mengatasi masalah service

### Jika database 'campusway_db' tidak ada:

1. Buka SQL Server Management Studio
2. Connect ke `localhost\SQLEXPRESS`
3. Buat database baru dengan nama `campusway_db`
4. Pastikan user `campusway_admin` memiliki akses ke database tersebut

### Jika user 'campusway_admin' tidak ada:

1. Buka SQL Server Management Studio
2. Connect ke `localhost\SQLEXPRESS`
3. Security → Logins → New Login
4. Buat login dengan nama `campusway_admin` dan password `P@ssw0rdBaru!2025`
5. Assign ke database `campusway_db` dengan role `db_owner`

## File yang Sudah Diperbaiki

1. `config/database.php` - Konfigurasi database untuk SQL Server Express
2. `test_sqlserver_express.php` - Script test koneksi
3. `start_sqlserver_express.bat` - Script untuk start service
4. `simple_connection_test.php` - Script test koneksi sederhana

## Langkah Selanjutnya

1. Start SQL Server Express service
2. Test koneksi database
3. Buat file .env dengan konfigurasi di atas
4. Jalankan `php artisan serve`
5. Buka browser ke `http://localhost:8000`
