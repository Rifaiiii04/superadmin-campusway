# 🚀 Setup API TKAWEB - Siswa

## 📋 **Persiapan Awal**

### **1. Pastikan Laravel Project Sudah Berjalan**

```bash
# Cek apakah Laravel sudah running
php artisan serve
```

### **2. Jalankan Migration**

```bash
# Jalankan semua migration yang diperlukan
php artisan migrate

# Atau jalankan satu per satu:
php artisan migrate --path=database/migrations/2025_01_01_000001_create_test_results_table.php
php artisan migrate --path=database/migrations/2025_01_01_000002_create_test_answers_table.php
php artisan migrate --path=database/migrations/2025_01_01_000003_add_status_to_students_table.php
php artisan migrate --path=database/migrations/2025_08_31_030500_create_results_table.php
php artisan migrate --path=database/migrations/2025_08_31_030501_create_recommendations_table.php
```

### **3. Jalankan Seeder**

```bash
# Jalankan seeder untuk mata pelajaran
php artisan db:seed --class=SubjectsSeeder

# Jalankan seeder untuk admin (jika belum ada)
php artisan db:seed --class=AdminSeeder

# Jalankan seeder untuk sample data (jika diperlukan)
php artisan db:seed --class=SampleDataSeeder
```

### **4. Install Package PDF**

```bash
# Install DomPDF untuk export PDF
composer require barryvdh/laravel-dompdf
```

---

## 🔧 **Konfigurasi Database**

### **Pastikan Tabel Berikut Sudah Ada:**

-   `students` - Data siswa (dengan kolom: name, nisn, school_id, kelas, email, phone, status)
-   `schools` - Data sekolah (dengan kolom: npsn, name, password_hash)
-   `questions` - Bank soal
-   `question_options` - Opsi jawaban
-   `test_results` - Hasil tes
-   `test_answers` - Jawaban tes
-   `subjects` - Mata pelajaran
-   `results` - Hasil per mata pelajaran
-   `recommendations` - Rekomendasi jurusan

### **Struktur Tabel yang Benar:**

#### **Tabel `students`:**

```sql
- id (bigint, primary key)
- name (varchar)                    -- Nama lengkap siswa
- nisn (varchar, unique)            -- NISN siswa
- school_id (bigint, foreign key)   -- ID sekolah (bukan NPSN)
- kelas (varchar)                   -- Kelas siswa
- email (varchar)                   -- Email siswa
- phone (varchar)                   -- Nomor handphone
- status (enum: registered, testing, completed)
- created_at (timestamp)
- updated_at (timestamp)
```

#### **Tabel `schools`:**

```sql
- id (bigint, primary key)
- npsn (varchar, unique)           -- NPSN sekolah
- name (varchar)                    -- Nama sekolah
- password_hash (varchar)           -- Password hash untuk sekolah
- created_at (timestamp)
- updated_at (timestamp)
```

---

## 🌐 **Testing API**

### **1. Test Registrasi Siswa**

```bash
curl -X POST http://127.0.0.1:8000/api/student/register \
  -H "Content-Type: application/json" \
  -d '{
    "nama_lengkap": "Ahmad Fadillah",
    "nisn": "1234567890",
    "npsn_sekolah": "12345678",
    "nama_sekolah": "SMA Negeri 1 Jakarta",
    "kelas": "XII IPA",
    "no_handphone": "081234567890",
    "email": "ahmad@email.com",
    "no_orang_tua": "081234567891"
  }'
```

### **2. Test Ambil Soal**

```bash
curl -X POST http://127.0.0.1:8000/api/student/questions \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": 1,
    "subjects": ["Bahasa Indonesia", "Matematika", "Bahasa Inggris", "Fisika", "Biologi"]
  }'
```

### **3. Test Submit Jawaban**

```bash
curl -X POST http://127.0.0.1:8000/api/student/submit-answers \
  -H "Content-Type: application/json" \
  -d '{
    "test_id": 1,
    "answers": [
      {"question_id": 1, "selected_option_id": 1},
      {"question_id": 2, "selected_option_id": 3}
    ]
  }'
```

---

## 🧪 **Debug Endpoints**

### **1. Test School Data**

```bash
curl -X GET http://127.0.0.1:8000/api/student/test-school-data
```

### **2. Test Registration Logic**

```bash
curl -X GET http://127.0.0.1:8000/api/student/test-registration
```

### **3. Test API Working**

```bash
curl -X GET http://127.0.0.1:8000/api/student/test
```

### **4. Get Available Subjects**

```bash
curl -X GET http://127.0.0.1:8000/api/student/subjects
```

### **5. Get Available Schools**

```bash
curl -X GET http://127.0.0.1:8000/api/student/schools
```

### **6. Get Student Status**

```bash
curl -X GET http://127.0.0.1:8000/api/student/student-status/1234567890
```

---

## 📁 **File yang Dibuat**

### **Controllers:**

-   `app/Http/Controllers/StudentApiController.php`

### **Models:**

-   `app/Models/TestResult.php`
-   `app/Models/TestAnswer.php`
-   `app/Models/Subject.php`
-   `app/Models/Result.php`
-   `app/Models/Recommendation.php`

### **Migrations:**

-   `database/migrations/2025_01_01_000001_create_test_results_table.php`
-   `database/migrations/2025_01_01_000002_create_test_answers_table.php`
-   `database/migrations/2025_01_01_000003_add_status_to_students_table.php`
-   `database/migrations/2025_01_01_000004_create_subjects_table.php`
-   `database/migrations/2025_08_31_030500_create_results_table.php`
-   `database/migrations/2025_08_31_030501_create_recommendations_table.php`

### **Seeders:**

-   `database/seeders/SubjectsSeeder.php`
-   `database/seeders/AdminSeeder.php`
-   `database/seeders/SampleDataSeeder.php`

### **Views:**

-   `resources/views/pdf/test-result.blade.php`

### **Routes:**

-   `routes/api.php` (sudah diupdate)

### **Dokumentasi:**

-   `STUDENT_API_DOCUMENTATION.md`

---

## ⚠️ **Troubleshooting**

### **1. Migration Error: Foreign Key Constraint**

```bash
# Jika ada error foreign key, gunakan NO ACTION
Schema::foreignId('selected_option_id')->constrained('question_options')->onDelete('no action');
```

### **2. Tabel Sudah Ada**

```bash
# Jika tabel subjects sudah ada, gunakan seeder saja
php artisan db:seed --class=SubjectsSeeder
```

### **3. Package PDF Error**

```bash
# Clear cache dan config
php artisan config:clear
php artisan cache:clear
composer dump-autoload
```

### **4. Error "NPSN tidak cocok dengan nama sekolah"**

**Penyebab:** Mismatch antara nama kolom database dan controller

**Solusi:**

-   Controller sudah diperbaiki untuk menggunakan kolom yang benar
-   Mapping: `nama_lengkap` → `name`, `npsn_sekolah` → `school_id`
-   Pastikan data sekolah di database sudah benar

### **5. Error 500 "Cannot insert NULL into column 'name'**

**Penyebab:** Controller mencoba insert ke kolom yang tidak ada

**Solusi:**

-   Controller sudah diperbaiki untuk mapping kolom yang benar
-   Gunakan endpoint `/test-school-data` untuk verifikasi data sekolah

---

## 🧪 **Testing dengan Postman**

### **Collection Postman:**

1. Import collection dari file yang disediakan
2. Set base URL: `http://127.0.0.1:8000/api/student`
3. Test semua endpoint secara berurutan

### **Environment Variables:**

-   `base_url`: `http://127.0.0.1:8000/api/student`
-   `student_id`: ID siswa setelah registrasi
-   `test_id`: ID tes setelah mengambil soal

### **Headers yang Diperlukan:**

```javascript
{
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}
```

### **Testing Registrasi:**

1. **Method:** `POST`
2. **URL:** `http://127.0.0.1:8000/api/student/register`
3. **Headers:** `Content-Type: application/json`
4. **Body (raw JSON):**

```json
{
    "nama_lengkap": "Ahmad Fadillah",
    "nisn": "1234567890",
    "npsn_sekolah": "12345678",
    "nama_sekolah": "SMA Negeri 1 Jakarta",
    "kelas": "XII IPA",
    "no_handphone": "081234567890",
    "email": "ahmad@email.com",
    "no_orang_tua": "081234567891"
}
```

**Expected Response:** `201 Created`

---

## 📱 **Integrasi Frontend**

### **Base URL API:**

```
http://127.0.0.1:8000/api/student
```

### **Response Format:**

```javascript
{
  "success": true/false,
  "message": "Pesan response",
  "data": { ... }
}
```

### **Error Response dengan Debug Info:**

```javascript
{
  "success": false,
  "message": "NPSN tidak cocok dengan nama sekolah",
  "debug": {
    "request_nama_sekolah": "SMA Negeri 1 Jakarta",
    "db_school_name": "SMA Negeri 1 Jakarta",
    "comparison": { ... }
  }
}
```

---

## 🔒 **Security & Validation**

### **Validasi yang Sudah Diimplementasi:**

-   ✅ NISN unik
-   ✅ NPSN sekolah valid
-   ✅ Format email dan nomor telepon
-   ✅ Siswa hanya bisa tes 1 kali
-   ✅ Validasi mata pelajaran wajib
-   ✅ Mapping kolom database yang benar

### **Rate Limiting:**

-   Belum diimplementasi (bisa ditambahkan sesuai kebutuhan)

### **CORS:**

-   Pastikan CORS dikonfigurasi untuk domain frontend

---

## 📊 **Monitoring & Logging**

### **Log Files:**

-   Semua error di-log ke `storage/logs/laravel.log`
-   Gunakan `Log::error()` untuk debugging
-   Request dan response di-log dengan detail lengkap

### **Database Logging:**

-   Query database bisa dimonitor dengan `DB::enableQueryLog()`

### **Debug Endpoints:**

-   Semua endpoint menyertakan informasi debug untuk troubleshooting
-   Data yang dikirim vs data di database
-   Perbandingan validasi step-by-step

---

## 🚀 **Deployment**

### **Production Checklist:**

1. ✅ Set `APP_ENV=production`
2. ✅ Set `APP_DEBUG=false`
3. ✅ Optimize Laravel: `php artisan optimize`
4. ✅ Set permission storage dan cache
5. ✅ Konfigurasi database production
6. ✅ Setup SSL/HTTPS

---

## 🔧 **Fixes yang Sudah Diterapkan**

### **1. Database Column Mapping**

-   ✅ Fixed mismatch antara controller field names dan database columns
-   ✅ Students table sekarang menggunakan `name`, `school_id`, `phone`
-   ✅ Proper foreign key relationships established

### **2. Validation Logic**

-   ✅ School validation sudah benar
-   ✅ Debug information ditambahkan ke semua error response
-   ✅ Logging yang detail untuk troubleshooting

### **3. Error Handling**

-   ✅ Semua error di-log dengan context lengkap
-   ✅ Debug endpoints untuk testing dan verification
-   ✅ Proper HTTP status codes

---

## 📞 **Support**

Jika ada masalah atau pertanyaan:

1. Cek log Laravel: `storage/logs/laravel.log`
2. Cek error response API
3. Gunakan debug endpoints untuk troubleshooting
4. Pastikan semua migration dan seeder berhasil
5. Hubungi tim development TKAWEB

---

**© 2025 TKAWEB - Test of Knowledge and Academic Web**
