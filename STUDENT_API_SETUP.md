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
```

### **3. Jalankan Seeder**

```bash
# Jalankan seeder untuk mata pelajaran
php artisan db:seed --class=SubjectsSeeder
```

### **4. Install Package PDF**

```bash
# Install DomPDF untuk export PDF
composer require barryvdh/laravel-dompdf
```

---

## 🔧 **Konfigurasi Database**

### **Pastikan Tabel Berikut Sudah Ada:**

-   `students` - Data siswa
-   `schools` - Data sekolah
-   `questions` - Bank soal
-   `question_options` - Opsi jawaban
-   `test_results` - Hasil tes
-   `test_answers` - Jawaban tes
-   `subjects` - Mata pelajaran

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

## 📁 **File yang Dibuat**

### **Controllers:**

-   `app/Http/Controllers/StudentApiController.php`

### **Models:**

-   `app/Models/TestResult.php`
-   `app/Models/TestAnswer.php`
-   `app/Models/Subject.php`

### **Migrations:**

-   `database/migrations/2025_01_01_000001_create_test_results_table.php`
-   `database/migrations/2025_01_01_000002_create_test_answers_table.php`
-   `database/migrations/2025_01_01_000003_add_status_to_students_table.php`

### **Seeders:**

-   `database/seeders/SubjectsSeeder.php`

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

---

## 📱 **Integrasi Frontend**

### **Base URL API:**

```
http://127.0.0.1:8000/api/student
```

### **Headers yang Diperlukan:**

```javascript
{
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}
```

### **Response Format:**

```javascript
{
  "success": true/false,
  "message": "Pesan response",
  "data": { ... }
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

### **Rate Limiting:**

-   Belum diimplementasi (bisa ditambahkan sesuai kebutuhan)

### **CORS:**

-   Pastikan CORS dikonfigurasi untuk domain frontend

---

## 📊 **Monitoring & Logging**

### **Log Files:**

-   Semua error di-log ke `storage/logs/laravel.log`
-   Gunakan `Log::error()` untuk debugging

### **Database Logging:**

-   Query database bisa dimonitor dengan `DB::enableQueryLog()`

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

## 📞 **Support**

Jika ada masalah atau pertanyaan:

1. Cek log Laravel: `storage/logs/laravel.log`
2. Cek error response API
3. Pastikan semua migration dan seeder berhasil
4. Hubungi tim development TKAWEB

---

**© 2025 TKAWEB - Test of Knowledge and Academic Web**
