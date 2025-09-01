# 📚 API Dokumentasi TKAWEB - Siswa

## 🎯 **Overview**
API ini digunakan untuk aplikasi siswa yang akan melakukan tes online TKAWEB. Siswa dapat mendaftar, mengambil soal, menjawab tes, dan melihat hasil dengan rekomendasi jurusan.

---

## 🔗 **Base URL**
```
http://127.0.0.1:8000/api/student
```

---

## 📋 **Endpoint List**

### 1. **Registrasi Siswa**
**POST** `/register`

**Deskripsi:** Mendaftarkan siswa baru untuk melakukan tes

**Request Body:**
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

**Response Success (201):**
```json
{
    "success": true,
    "message": "Registrasi siswa berhasil",
    "data": {
        "student_id": 1,
        "nama_lengkap": "Ahmad Fadillah",
        "nisn": "1234567890",
        "nama_sekolah": "SMA Negeri 1 Jakarta"
    }
}
```

**Response Error (422):**
```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "nisn": ["NISN sudah digunakan"],
        "npsn_sekolah": ["NPSN sekolah tidak ditemukan"]
    }
}
```

---

### 2. **Ambil Soal Tes**
**POST** `/questions`

**Deskripsi:** Mengambil soal tes berdasarkan mata pelajaran yang dipilih

**Request Body:**
```json
{
    "student_id": 1,
    "subjects": [
        "Bahasa Indonesia",
        "Matematika", 
        "Bahasa Inggris",
        "Fisika",
        "Biologi"
    ]
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Soal berhasil diambil",
    "data": {
        "test_id": 1,
        "questions": {
            "Bahasa Indonesia": [
                {
                    "id": 1,
                    "question_text": "Apa arti dari kata 'melaksanakan'?",
                    "media_url": null,
                    "options": [
                        {"id": 1, "option_text": "Melakukan"},
                        {"id": 2, "option_text": "Membuat"},
                        {"id": 3, "option_text": "Menggambar"},
                        {"id": 4, "option_text": "Menulis"}
                    ]
                }
            ]
        },
        "start_time": "2025-01-01T10:00:00.000000Z",
        "time_limit": 120
    }
}
```

---

### 3. **Submit Jawaban**
**POST** `/submit-answers`

**Deskripsi:** Submit semua jawaban siswa dan hitung skor

**Request Body:**
```json
{
    "test_id": 1,
    "answers": [
        {
            "question_id": 1,
            "selected_option_id": 1
        },
        {
            "question_id": 2,
            "selected_option_id": 3
        }
    ]
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Jawaban berhasil disimpan",
    "data": {
        "test_id": 1,
        "scores": [
            {
                "subject": "Bahasa Indonesia",
                "score": 85.5,
                "correct_answers": 17,
                "total_questions": 20,
                "percentage": 85.0
            }
        ],
        "total_score": 425.5,
        "recommendations": {
            "strengths": ["Bahasa Indonesia", "Matematika"],
            "weaknesses": ["Fisika"],
            "recommendations": [
                "Teknik (Teknik Mesin, Teknik Elektro, Teknik Sipil)"
            ]
        }
    }
}
```

---

### 4. **Auto-Save Jawaban**
**POST** `/auto-save`

**Deskripsi:** Menyimpan jawaban secara otomatis (untuk timer dan auto-save)

**Request Body:**
```json
{
    "test_id": 1,
    "question_id": 1,
    "selected_option_id": 1
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Jawaban berhasil disimpan"
}
```

---

### 5. **Lihat Hasil Tes**
**GET** `/results/{testId}`

**Deskripsi:** Melihat hasil tes lengkap dengan rekomendasi

**Response Success (200):**
```json
{
    "success": true,
    "message": "Hasil tes berhasil diambil",
    "data": {
        "student": {
            "nama_lengkap": "Ahmad Fadillah",
            "nisn": "1234567890",
            "nama_sekolah": "SMA Negeri 1 Jakarta",
            "kelas": "XII IPA"
        },
        "test_info": {
            "start_time": "2025-01-01T10:00:00.000000Z",
            "end_time": "2025-01-01T12:00:00.000000Z",
            "duration": 120
        },
        "scores": [
            {
                "subject": "Bahasa Indonesia",
                "score": 85.5,
                "correct_answers": 17,
                "total_questions": 20,
                "percentage": 85.0
            }
        ],
        "total_score": 425.5,
        "average_score": 85.1,
        "recommendations": {
            "strengths": ["Bahasa Indonesia", "Matematika"],
            "weaknesses": ["Fisika"],
            "recommendations": [
                "Teknik (Teknik Mesin, Teknik Elektro, Teknik Sipil)"
            ]
        }
    }
}
```

---

### 6. **Export PDF Hasil**
**GET** `/export-pdf/{testId}`

**Deskripsi:** Download hasil tes dalam format PDF

**Response:** File PDF yang dapat didownload

---

## 🗄️ **Database Schema**

### **Tabel: students**
```sql
- id (bigint, primary key)
- nama_lengkap (varchar)
- nisn (varchar, unique)
- npsn_sekolah (varchar)
- nama_sekolah (varchar)
- kelas (varchar)
- no_handphone (varchar)
- email (varchar)
- no_orang_tua (varchar)
- status (enum: registered, testing, completed)
- created_at (timestamp)
- updated_at (timestamp)
```

### **Tabel: test_results**
```sql
- id (bigint, primary key)
- student_id (bigint, foreign key)
- subjects (json) - Array mata pelajaran
- start_time (datetime)
- end_time (datetime, nullable)
- status (enum: ongoing, completed, expired)
- scores (json, nullable) - Skor per mata pelajaran
- total_score (decimal, nullable)
- recommendations (json, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### **Tabel: test_answers**
```sql
- id (bigint, primary key)
- test_result_id (bigint, foreign key)
- question_id (bigint, foreign key)
- selected_option_id (bigint, foreign key)
- answered_at (datetime)
- created_at (timestamp)
- updated_at (timestamp)
```

### **Tabel: subjects**
```sql
- id (bigint, primary key)
- name (varchar, unique)
- code (varchar, unique)
- description (text, nullable)
- is_required (boolean)
- is_active (boolean)
- created_at (timestamp)
- updated_at (timestamp)
```

---

## 🔐 **Validasi & Business Rules**

### **Registrasi Siswa:**
- NISN harus unik (tidak boleh dipakai 2 kali)
- NPSN harus cocok dengan nama sekolah
- Format data harus valid (email, nomor telepon)

### **Tes:**
- Siswa hanya bisa tes 1 kali
- Mata pelajaran wajib: Bahasa Indonesia, Matematika, Bahasa Inggris
- Pilih 2 mapel tambahan sesuai minat
- Total 5 mata pelajaran
- 20 soal per mata pelajaran
- Timer: 2 jam (120 menit)

### **Jawaban:**
- Auto-save setiap kali siswa memilih jawaban
- Bisa diubah sebelum submit final
- Setelah submit tidak bisa diubah

---

## 📊 **Algoritma Scoring**

### **Perhitungan Skor:**
```
Skor = (Jawaban Benar / Total Soal) × 100
Total Skor = Σ Skor per Mata Pelajaran
Rata-rata = Total Skor / Jumlah Mata Pelajaran
```

### **Kategori Kekuatan & Kelemahan:**
- **Kekuatan:** Skor ≥ 80%
- **Sedang:** Skor 60-79%
- **Kelemahan:** Skor < 60%

---

## 🎓 **Rekomendasi Jurusan**

### **Berdasarkan Mata Pelajaran Unggulan:**

1. **Matematika + Fisika** → Teknik (Mesin, Elektro, Sipil)
2. **Biologi + Kimia** → Kedokteran, Farmasi, Biologi
3. **Bahasa Indonesia + Bahasa Inggris** → Sastra, Pendidikan Bahasa, Komunikasi
4. **Ekonomi + Matematika** → Ekonomi, Manajemen, Akuntansi

---

## ⚠️ **Error Handling**

### **HTTP Status Codes:**
- `200` - Success
- `201` - Created
- `400` - Bad Request (Business Logic Error)
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

### **Error Response Format:**
```json
{
    "success": false,
    "message": "Pesan error dalam bahasa Indonesia",
    "errors": {
        "field": ["Detail error"]
    }
}
```

---

## 🚀 **Cara Penggunaan**

### **1. Registrasi Siswa**
```javascript
const response = await fetch('/api/student/register', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        nama_lengkap: "Ahmad Fadillah",
        nisn: "1234567890",
        npsn_sekolah: "12345678",
        nama_sekolah: "SMA Negeri 1 Jakarta",
        kelas: "XII IPA",
        no_handphone: "081234567890",
        email: "ahmad@email.com",
        no_orang_tua: "081234567891"
    })
});

const data = await response.json();
```

### **2. Ambil Soal**
```javascript
const response = await fetch('/api/student/questions', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        student_id: data.data.student_id,
        subjects: ["Bahasa Indonesia", "Matematika", "Bahasa Inggris", "Fisika", "Biologi"]
    })
});
```

### **3. Submit Jawaban**
```javascript
const response = await fetch('/api/student/submit-answers', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        test_id: testId,
        answers: answers
    })
});
```

---

## 📝 **Catatan Penting**

1. **CORS:** Pastikan CORS dikonfigurasi untuk domain frontend
2. **Rate Limiting:** Implementasikan rate limiting untuk mencegah spam
3. **Logging:** Semua error di-log untuk debugging
4. **Validation:** Validasi dilakukan di backend dan frontend
5. **Security:** Tidak ada autentikasi khusus, hanya validasi data

---

## 🔧 **Setup & Installation**

### **1. Jalankan Migration:**
```bash
php artisan migrate
```

### **2. Install Package PDF (jika belum):**
```bash
composer require barryvdh/laravel-dompdf
```

### **3. Publish Config (jika perlu):**
```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

---

## 📞 **Support**

Untuk pertanyaan atau masalah teknis, silakan hubungi tim development TKAWEB.

---

**© 2025 TKAWEB - Test of Knowledge and Academic Web**
