# 🔧 Perbaikan Halaman Sekolah di VPS Production

## 📋 **Masalah yang Ditemukan:**

1. **Error JavaScript**: `Cannot read properties of undefined (reading 'data')`
2. **Struktur Database**: Tabel `schools` memiliki field `password_hash` dan `password` yang konflik
3. **Model tidak sesuai**: Model School hanya menggunakan `password` tapi database memiliki `password_hash`

## 🚀 **Langkah-langkah Perbaikan:**

### **Opsi 1: Perbaikan Otomatis (Recommended)**

1. **Upload file perbaikan ke VPS:**

    ```bash
    # Upload file perbaikan
    scp fix_vps_schools_database.php root@your-vps-ip:/path/to/superadmin-backend/
    scp quick_fix_vps_schools.sh root@your-vps-ip:/path/to/superadmin-backend/
    ```

2. **Jalankan perbaikan di VPS:**

    ```bash
    # SSH ke VPS
    ssh root@your-vps-ip

    # Navigate ke project directory
    cd /path/to/superadmin-backend

    # Jalankan perbaikan database
    php fix_vps_schools_database.php

    # Atau jalankan script otomatis
    chmod +x quick_fix_vps_schools.sh
    ./quick_fix_vps_schools.sh
    ```

### **Opsi 2: Perbaikan Manual**

1. **Perbaiki database secara manual:**

    ```sql
    -- Copy data dari password_hash ke password
    UPDATE schools SET password = password_hash WHERE password IS NULL;

    -- Buat password field NOT NULL
    ALTER TABLE schools MODIFY COLUMN password VARCHAR(255) NOT NULL;

    -- Hapus password_hash column
    ALTER TABLE schools DROP COLUMN password_hash;
    ```

2. **Upload file yang sudah diperbaiki:**

    ```bash
    # Upload controller
    scp app/Http/Controllers/SchoolController.php root@your-vps-ip:/path/to/superadmin-backend/app/Http/Controllers/

    # Upload component
    scp resources/js/Pages/SuperAdmin/Schools.jsx root@your-vps-ip:/path/to/superadmin-backend/resources/js/Pages/SuperAdmin/

    # Upload routes
    scp routes/web.php root@your-vps-ip:/path/to/superadmin-backend/routes/

    # Upload model
    scp app/Models/School.php root@your-vps-ip:/path/to/superadmin-backend/app/Models/
    ```

3. **Clear cache dan rebuild:**

    ```bash
    # Clear Laravel cache
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear

    # Rebuild frontend assets
    npm run build

    # Restart services
    systemctl restart apache2
    systemctl restart php8.1-fpm
    ```

## ✅ **Verifikasi Perbaikan:**

1. **Akses halaman Sekolah**: Buka `/schools` di browser
2. **Test CRUD operations**:
    - Tambah sekolah baru
    - Edit sekolah yang ada
    - Hapus sekolah
    - Import dari CSV
3. **Test search functionality**: Gunakan search box untuk mencari sekolah

## 🔍 **Troubleshooting:**

Jika masih ada masalah:

1. **Check error logs:**

    ```bash
    tail -f /var/log/apache2/error.log
    tail -f storage/logs/laravel.log
    ```

2. **Check database structure:**

    ```sql
    DESCRIBE schools;
    SELECT COUNT(*) FROM schools;
    ```

3. **Check browser console** untuk error JavaScript

## 📝 **File yang Diperbaiki:**

-   ✅ `app/Http/Controllers/SchoolController.php` - Controller baru untuk CRUD schools
-   ✅ `resources/js/Pages/SuperAdmin/Schools.jsx` - Komponen frontend yang diperbaiki
-   ✅ `routes/web.php` - Routes yang menggunakan controller
-   ✅ `app/Models/School.php` - Model yang disesuaikan dengan database
-   ✅ `database/migrations/2025_01_15_000000_fix_schools_password_field.php` - Migration untuk perbaikan database

## 🎯 **Hasil yang Diharapkan:**

-   ✅ Halaman Sekolah dapat diakses tanpa error
-   ✅ CRUD operations berfungsi dengan baik
-   ✅ Import CSV berfungsi
-   ✅ Search/filter berfungsi
-   ✅ Responsive design untuk mobile dan desktop
