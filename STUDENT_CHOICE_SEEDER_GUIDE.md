# 🎯 Student Choice Seeder Guide

## 📋 Overview

Script ini dibuat untuk mengisi data pilihan jurusan siswa ke database agar fitur pilihan jurusan bisa ditest dengan benar.

## 🚀 Cara Menjalankan

### **Opsi 1: Laravel Seeder (Recommended)**

```bash
# Masuk ke direktori backend
cd superadmin-backend

# Jalankan seeder
php artisan db:seed --class=StudentChoiceSeeder
```

### **Opsi 2: Manual PHP Script**

```bash
# Masuk ke direktori backend
cd superadmin-backend

# Jalankan script manual
php run_student_choice_seeder.php
```

### **Opsi 3: Windows Batch Script**

```bash
# Double-click file atau jalankan di command prompt
run_seeder.bat
```

### **Opsi 4: SQL Script Manual**

```sql
-- Buka SQL Server Management Studio
-- Connect ke database
-- Jalankan file: seed_student_choices.sql
```

## 🔍 Mengecek Data

### **Cek Data yang Sudah Ada**

```bash
# Jalankan script pengecekan
php check_student_choices.php
```

### **Cek Manual di Database**

```sql
-- Cek berapa siswa yang sudah memilih jurusan
SELECT
    COUNT(*) as total_choices,
    COUNT(DISTINCT student_id) as students_with_choice
FROM student_choices;

-- Cek berapa siswa yang belum memilih jurusan
SELECT
    COUNT(*) as students_without_choice
FROM students s
LEFT JOIN student_choices sc ON s.id = sc.student_id
WHERE sc.student_id IS NULL;

-- Lihat data pilihan jurusan
SELECT
    sc.id,
    s.name as student_name,
    s.nisn,
    mr.major_name,
    mr.category,
    sc.created_at
FROM student_choices sc
JOIN students s ON sc.student_id = s.id
JOIN major_recommendations mr ON sc.major_id = mr.id
ORDER BY sc.id;
```

## 📊 Data yang Dibuat

### **StudentChoiceSeeder akan:**

1. **Mengambil 5 siswa pertama** dari database
2. **Mengambil 10 jurusan aktif** dari database
3. **Membuat pilihan jurusan** untuk sebagian siswa (bukan semua)
4. **Memberikan variasi** - beberapa siswa punya pilihan, beberapa tidak

### **Contoh Data yang Dibuat:**

```
✅ Created choice for student John Doe -> Teknik Informatika
✅ Created choice for student Jane Smith -> Teknik Sipil
⏭️  Skipping student Bob Johnson (no choice)
✅ Created choice for student Alice Brown -> Teknik Elektro
⏭️  Skipping student Charlie Wilson (no choice)
```

## 🧪 Testing

### **1. Cek Data di Database**

```bash
php check_student_choices.php
```

### **2. Test API Endpoint**

```bash
# Test endpoint major-status
curl http://127.0.0.1:8000/api/web/major-status/1

# Response jika sudah pilih:
{
  "success": true,
  "data": {
    "has_choice": true,
    "selected_major_id": 1
  }
}

# Response jika belum pilih:
{
  "success": true,
  "data": {
    "has_choice": false,
    "selected_major_id": null
  }
}
```

### **3. Test Frontend**

1. **Login sebagai siswa** yang sudah ada di database
2. **Cek status pilihan jurusan** - harus menampilkan "Jurusan Dipilih" atau "Pilih Jurusan"
3. **Refresh halaman** - status harus tetap sama
4. **Ubah pilihan jurusan** - harus berfungsi dengan benar

## 🔧 Troubleshooting

### **Error: No students found**

```bash
# Jalankan seeder siswa terlebih dahulu
php artisan db:seed --class=StudentSeeder
```

### **Error: No majors found**

```bash
# Jalankan seeder jurusan terlebih dahulu
php artisan db:seed --class=MajorRecommendationSeeder
```

### **Error: Database connection**

```bash
# Cek koneksi database
php artisan migrate:status

# Jika perlu, jalankan migrasi
php artisan migrate
```

### **Error: Permission denied**

```bash
# Pastikan file bisa dijalankan
chmod +x run_student_choice_seeder.php
chmod +x check_student_choices.php
```

## 📈 Expected Results

### **Setelah menjalankan seeder:**

-   **Total students**: 5 (atau sesuai data yang ada)
-   **Students with choice**: 3-4 (sebagian besar)
-   **Students without choice**: 1-2 (untuk testing)
-   **Total choices**: 3-4

### **Di Frontend:**

-   **Siswa dengan pilihan**: Tombol "Jurusan Dipilih" + "Lihat Detail"
-   **Siswa tanpa pilihan**: Tombol "Pilih Jurusan" + "Lihat Detail"
-   **Refresh halaman**: Status tetap sama
-   **Ubah pilihan**: Berfungsi dengan benar

## 🎯 Next Steps

1. **Jalankan seeder** menggunakan salah satu opsi di atas
2. **Cek data** dengan `php check_student_choices.php`
3. **Test frontend** dengan login sebagai siswa
4. **Verify** bahwa status pilihan jurusan ditampilkan dengan benar
5. **Test** fitur pilih/ubah jurusan

## 📝 Notes

-   **Seeder aman dijalankan berkali-kali** - tidak akan duplikasi data
-   **Data yang dibuat adalah sample data** untuk testing
-   **Untuk production**, gunakan data real dari form registrasi
-   **Jika ada error**, cek log Laravel di `storage/logs/laravel.log`
