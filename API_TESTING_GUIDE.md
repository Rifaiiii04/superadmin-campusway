# 🧪 **Panduan Testing API TKAWEB - Siswa**

## 🚨 **Error yang Sering Terjadi:**

### **MethodNotAllowedHttpException**
```
The GET method is not supported for route api/student/questions. 
Supported methods: POST.
```

**Penyebab:** Mencoba mengakses endpoint POST dengan method GET
**Solusi:** Gunakan method yang benar sesuai endpoint

---

## 🔗 **Endpoint yang Tersedia:**

### **GET Endpoints (untuk testing & debugging):**
- ✅ **GET** `/api/student/test` - Test endpoint
- ✅ **GET** `/api/student/subjects` - Daftar mata pelajaran
- ✅ **GET** `/api/student/schools` - Daftar sekolah
- ✅ **GET** `/api/student/student-status/{nisn}` - Status siswa
- ✅ **GET** `/api/student/results/{testId}` - Hasil tes
- ✅ **GET** `/api/student/export-pdf/{testId}` - Download PDF

### **POST Endpoints (untuk operasi utama):**
- ✅ **POST** `/api/student/register` - Registrasi siswa
- ✅ **POST** `/api/student/questions` - Ambil soal tes
- ✅ **POST** `/api/student/submit-answers` - Submit jawaban
- ✅ **POST** `/api/student/auto-save` - Auto-save jawaban

---

## 🧪 **Cara Testing yang Benar:**

### **1. Test API Berfungsi (GET)**
```bash
# Test endpoint
curl -X GET http://127.0.0.1:8000/api/student/test

# Daftar mata pelajaran
curl -X GET http://127.0.0.1:8000/api/student/subjects

# Daftar sekolah
curl -X GET http://127.0.0.1:8000/api/student/schools
```

### **2. Registrasi Siswa (POST)**
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

### **3. Ambil Soal (POST)**
```bash
curl -X POST http://127.0.0.1:8000/api/student/questions \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": 1,
    "subjects": ["Bahasa Indonesia", "Matematika", "Bahasa Inggris", "Fisika", "Biologi"]
  }'
```

---

## 🌐 **Testing dengan Browser:**

### **Endpoint yang Bisa Diakses via Browser (GET):**
1. **Test API:** `http://127.0.0.1:8000/api/student/test`
2. **Daftar Mata Pelajaran:** `http://127.0.0.1:8000/api/student/subjects`
3. **Daftar Sekolah:** `http://127.0.0.1:8000/api/student/schools`
4. **Status Siswa:** `http://127.0.0.1:8000/api/student/student-status/1234567890`

### **Endpoint yang TIDAK Bisa Diakses via Browser (POST):**
- ❌ `http://127.0.0.1:8000/api/student/register`
- ❌ `http://127.0.0.1:8000/api/student/questions`
- ❌ `http://127.0.0.1:8000/api/student/submit-answers`

---

## 🛠️ **Tools untuk Testing:**

### **1. Postman/Insomnia (Recommended)**
- Import collection yang disediakan
- Set environment variables
- Test semua endpoint secara berurutan

### **2. cURL (Command Line)**
- Bagus untuk testing cepat
- Pastikan method dan headers benar

### **3. Browser (Hanya untuk GET)**
- Test endpoint yang mendukung GET
- Lihat response JSON

### **4. Frontend Testing**
- Buat form sederhana
- Test dengan JavaScript fetch

---

## 📋 **Step-by-Step Testing:**

### **Step 1: Test API Berfungsi**
```bash
GET http://127.0.0.1:8000/api/student/test
```
**Expected Response:**
```json
{
  "success": true,
  "message": "Student API is working!",
  "endpoints": { ... }
}
```

### **Step 2: Lihat Data yang Tersedia**
```bash
GET http://127.0.0.1:8000/api/student/subjects
GET http://127.0.0.1:8000/api/student/schools
```

### **Step 3: Registrasi Siswa**
```bash
POST http://127.0.0.1:8000/api/student/register
```
**Body:**
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

### **Step 4: Ambil Soal**
```bash
POST http://127.0.0.1:8000/api/student/questions
```
**Body:**
```json
{
  "student_id": 1,
  "subjects": ["Bahasa Indonesia", "Matematika", "Bahasa Inggris", "Fisika", "Biologi"]
}
```

---

## ⚠️ **Troubleshooting:**

### **1. MethodNotAllowedHttpException**
**Gejala:** Error "GET method is not supported"
**Solusi:** 
- Gunakan method yang benar (POST untuk register/questions)
- Atau gunakan endpoint GET yang tersedia

### **2. 404 Not Found**
**Gejala:** Route tidak ditemukan
**Solusi:**
- Pastikan Laravel server running
- Cek routes dengan `php artisan route:list`
- Clear cache: `php artisan route:clear`

### **3. 422 Validation Error**
**Gejala:** Data tidak valid
**Solusi:**
- Cek format data yang dikirim
- Pastikan semua field required terisi
- Cek validasi di controller

### **4. 500 Server Error**
**Gejala:** Error internal server
**Solusi:**
- Cek log Laravel: `storage/logs/laravel.log`
- Pastikan database connection
- Cek migration sudah dijalankan

---

## 🔍 **Debugging Tips:**

### **1. Cek Routes**
```bash
php artisan route:list --path=api/student
```

### **2. Cek Log Laravel**
```bash
tail -f storage/logs/laravel.log
```

### **3. Test Database Connection**
```bash
php artisan tinker
DB::connection()->getPdo();
```

### **4. Cek Migration Status**
```bash
php artisan migrate:status
```

---

## 📱 **Testing dengan Frontend:**

### **JavaScript Example:**
```javascript
// Test endpoint
const testResponse = await fetch('/api/student/test');
const testData = await testResponse.json();
console.log('Test:', testData);

// Get subjects
const subjectsResponse = await fetch('/api/student/subjects');
const subjectsData = await subjectsResponse.json();
console.log('Subjects:', subjectsData);

// Register student
const registerResponse = await fetch('/api/student/register', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        nama_lengkap: "Ahmad Fadillah",
        nisn: "1234567890",
        // ... data lainnya
    })
});
const registerData = await registerResponse.json();
console.log('Register:', registerData);
```

---

## ✅ **Checklist Testing:**

- [ ] API test endpoint berfungsi
- [ ] Daftar mata pelajaran bisa diambil
- [ ] Daftar sekolah bisa diambil
- [ ] Registrasi siswa berhasil
- [ ] Ambil soal berhasil
- [ ] Submit jawaban berhasil
- [ ] Lihat hasil berhasil
- [ ] Download PDF berhasil

---

## 🎯 **Kesimpulan:**

**Untuk menghindari MethodNotAllowedHttpException:**

1. **Gunakan GET untuk:** test, subjects, schools, student-status
2. **Gunakan POST untuk:** register, questions, submit-answers, auto-save
3. **Test dengan browser hanya untuk endpoint GET**
4. **Gunakan Postman/Insomnia untuk endpoint POST**

**API sudah siap dan berfungsi dengan baik!** 🚀
