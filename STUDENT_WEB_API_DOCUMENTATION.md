# 📚 STUDENT WEB API DOCUMENTATION

## 🎯 Overview

API untuk web TKA (Tes Kemampuan Akademik) yang memungkinkan siswa login dan memilih jurusan berdasarkan mata pelajaran yang perlu dipelajari.

## 🔗 Base URL

```
http://127.0.0.1:8000/api/web
```

## 📋 API Endpoints

### 1. 📝 Student Registration

**POST** `/register-student`

**Request Body:**

```json
{
    "nisn": "1234567890",
    "name": "John Doe",
    "npsn_sekolah": "12345678",
    "nama_sekolah": "SMA Negeri 1 Jakarta",
    "kelas": "XII IPA 1",
    "email": "john@example.com",
    "phone": "081234567890",
    "parent_phone": "081234567891",
    "password": "password_siswa"
}
```

**Response Success (201):**

```json
{
    "success": true,
    "message": "Registrasi berhasil",
    "data": {
        "student": {
            "id": 1,
            "nisn": "1234567890",
            "name": "John Doe",
            "kelas": "XII IPA 1",
            "email": "john@example.com",
            "phone": "081234567890",
            "parent_phone": "081234567891",
            "school_name": "SMA Negeri 1 Jakarta",
            "has_choice": false
        }
    }
}
```

**Response Error (400):**

```json
{
    "success": false,
    "message": "NPSN tidak cocok dengan nama sekolah"
}
```

**Response Error (401):**

```json
{
    "success": false,
    "message": "Password salah"
}
```

**Response Error (404):**

```json
{
    "success": false,
    "message": "NPSN sekolah tidak ditemukan"
}
```

**Response Error (422):**

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "nisn": ["NISN sudah terdaftar"],
        "email": ["Format email tidak valid"]
    }
}
```

---

### 2. 🏫 Get Available Schools

**GET** `/schools`

**Response Success (200):**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "npsn": "12345678",
            "name": "SMA Negeri 1 Jakarta"
        },
        {
            "id": 2,
            "npsn": "87654321",
            "name": "SMA Negeri 2 Bandung"
        }
    ]
}
```

---

### 3. 🔐 Student Login

**POST** `/login`

**Requestt Body:**

```json
{
    "nisn": "1234567890",
    "password": "password_siswa"
}
```

**Response Success (200):**

```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "student": {
            "id": 1,
            "nisn": "1234567890",
            "name": "John Doe",
            "kelas": "XII IPA 1",
            "email": "john@example.com",
            "phone": "081234567890",
            "parent_phone": "081234567891",
            "school_name": "SMA Negeri 1 Jakarta",
            "has_choice": false
        }
    }
}
```

**Response Error (404):**

```json
{
    "success": false,
    "message": "NISN tidak ditemukan"
}
```

**Response Error (401):**

```json
{
    "success": false,
    "message": "Password salah"
}
```

---

### 4. 📚 Get All Active Majors

**GET** `/majors`

**Response Success (200):**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "major_name": "Teknik Informatika",
            "description": "Jurusan yang mempelajari teknologi informasi",
            "career_prospects": "Software Engineer, Data Scientist, Web Developer",
            "category": "Saintek"
        },
        {
            "id": 2,
            "major_name": "Ekonomi",
            "description": "Jurusan yang mempelajari ilmu ekonomi",
            "career_prospects": "Ekonom, Analis Keuangan, Konsultan",
            "category": "Soshum"
        }
    ]
}
```

---

### 5. 🔍 Get Major Details

**GET** `/majors/{id}`

**Response Success (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "major_name": "Teknik Informatika",
        "description": "Jurusan yang mempelajari teknologi informasi",
        "career_prospects": "Software Engineer, Data Scientist, Web Developer",
        "category": "Saintek",
        "subjects": {
            "required": ["Matematika", "Bahasa Inggris", "Bahasa Indonesia"],
            "preferred": ["Matematika Tingkat Lanjut", "Fisika", "Kimia"],
            "kurikulum_merdeka": [
                "Matematika Tingkat Lanjut",
                "Fisika",
                "Kimia"
            ],
            "kurikulum_2013_ipa": ["Matematika", "Fisika", "Kimia"],
            "kurikulum_2013_ips": ["Matematika", "Ekonomi", "Sejarah"],
            "kurikulum_2013_bahasa": [
                "Matematika",
                "Bahasa Indonesia",
                "Bahasa Inggris"
            ]
        }
    }
}
```

**Response Error (404):**

```json
{
    "success": false,
    "message": "Jurusan tidak ditemukan"
}
```

---

### 6. ✅ Choose Major

**POST** `/choose-major`

**Request Body:**

```json
{
    "student_id": 1,
    "major_id": 1
}
```

**Response Success (200):**

```json
{
    "success": true,
    "message": "Pilihan jurusan berhasil disimpan",
    "data": {
        "major_name": "Teknik Informatika",
        "chosen_at": "2025-01-01T10:00:00.000000Z"
    }
}
```

**Response Error (400):**

```json
{
    "success": false,
    "message": "Siswa sudah memilih jurusan sebelumnya"
}
```

---

### 7. 🔍 Check Student's Major Status

**GET** `/major-status/{studentId}`

**Response Success (200):**

```json
{
    "success": true,
    "data": {
        "has_choice": true,
        "selected_major_id": 1
    }
}
```

**Response Success - No Choice (200):**

```json
{
    "success": true,
    "data": {
        "has_choice": false,
        "selected_major_id": null
    }
}
```

---

### 8. 📖 Get Student's Chosen Major

**GET** `/student-choice/{studentId}`

**Response Success (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "major": {
            "id": 1,
            "major_name": "Teknik Informatika",
            "description": "Jurusan yang mempelajari teknologi informasi",
            "career_prospects": "Software Engineer, Data Scientist, Web Developer",
            "category": "Saintek",
            "subjects": {
                "required": [
                    "Matematika",
                    "Bahasa Inggris",
                    "Bahasa Indonesia"
                ],
                "preferred": ["Matematika Tingkat Lanjut", "Fisika", "Kimia"],
                "kurikulum_merdeka": [
                    "Matematika Tingkat Lanjut",
                    "Fisika",
                    "Kimia"
                ],
                "kurikulum_2013_ipa": ["Matematika", "Fisika", "Kimia"],
                "kurikulum_2013_ips": ["Matematika", "Ekonomi", "Sejarah"],
                "kurikulum_2013_bahasa": [
                    "Matematika",
                    "Bahasa Indonesia",
                    "Bahasa Inggris"
                ]
            }
        },
        "chosen_at": "2025-01-01T10:00:00.000000Z"
    }
}
```

**Response Error (404):**

```json
{
    "success": false,
    "message": "Siswa belum memilih jurusan"
}
```

---

### 9. 🔄 Change Major Choice

**POST** `/change-major`

**Request Body:**

```json
{
    "student_id": 1,
    "major_id": 2
}
```

**Response Success (200):**

```json
{
    "success": true,
    "message": "Pilihan jurusan berhasil diubah",
    "data": {
        "major_name": "Ekonomi",
        "updated_at": "2025-01-01T10:00:00.000000Z"
    }
}
```

---

### 7. 👤 Get Student Profile

**GET** `/student-profile/{studentId}`

**Response Success (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "nisn": "1234567890",
        "name": "John Doe",
        "kelas": "XII IPA 1",
        "email": "john@example.com",
        "phone": "081234567890",
        "parent_phone": "081234567891",
        "status": "active",
        "school": {
            "id": 1,
            "name": "SMA Negeri 1 Jakarta",
            "npsn": "20100101"
        },
        "major_choice": {
            "id": 1,
            "major_name": "Teknik Informatika",
            "category": "Saintek",
            "chosen_at": "2025-01-01T10:00:00.000000Z"
        }
    }
}
```

---

## 🔄 User Flow

### 1. **Registrasi Siswa**

```
Siswa → Input data lengkap → Register API → Response dengan data siswa
```

### 2. **Login Siswa**

```
Siswa → Input NISN + Password → Login API → Response dengan data siswa
```

### 3. **Lihat Daftar Sekolah**

```
Siswa → Get Schools API → Response daftar sekolah tersedia
```

### 4. **Lihat Daftar Jurusan**

```
Siswa → Get Majors API → Response daftar jurusan aktif
```

### 5. **Lihat Detail Jurusan**

```
Siswa → Klik jurusan → Get Major Details API → Response detail + mata pelajaran
```

### 6. **Pilih Jurusan**

```
Siswa → Pilih jurusan → Choose Major API → Response konfirmasi
```

### 7. **Cek Status Pilihan**

```
Siswa → Check Major Status API → Response status pilihan (true/false)
```

### 8. **Lihat Pilihan**

```
Siswa → Get Student Choice API → Response pilihan jurusan + mata pelajaran
```

---

## 🧪 Testing dengan Postman

### 1. **Test Registration**

```
POST http://127.0.0.1:8000/api/web/register-student
Content-Type: application/json

{
    "nisn": "1234567890",
    "name": "John Doe",
    "npsn_sekolah": "12345678",
    "nama_sekolah": "SMA Negeri 1 Jakarta",
    "kelas": "XII IPA 1",
    "email": "john@example.com",
    "phone": "081234567890",
    "parent_phone": "081234567891",
    "password": "password_siswa"
}
```

### 2. **Test Login**

```
POST http://127.0.0.1:8000/api/web/login
Content-Type: application/json

{
    "nisn": "1234567890",
    "password": "password_siswa"
}
```

### 3. **Test Get Schools**

```
GET http://127.0.0.1:8000/api/web/schools
```

### 4. **Test Get Majors**

```
GET http://127.0.0.1:8000/api/web/majors
```

### 5. **Test Get Major Details**

```
GET http://127.0.0.1:8000/api/web/majors/1
```

### 6. **Test Choose Major**

```
POST http://127.0.0.1:8000/api/web/choose-major
Content-Type: application/json

{
    "student_id": 1,
    "major_id": 1
}
```

---

## 📝 Notes

### **Authentication:**

-   Siswa login menggunakan **NISN** dan **password pribadi**
-   Setiap siswa memiliki password sendiri yang dibuat saat registrasi

### **Business Rules:**

-   Siswa hanya bisa memilih **1 jurusan**
-   Jika sudah memilih, harus menggunakan endpoint **change-major** untuk mengubah
-   Hanya jurusan **aktif** yang bisa dipilih

### **Mata Pelajaran:**

-   **Mata Pelajaran Wajib**: Sama untuk semua jurusan (Matematika, Bahasa Inggris, Bahasa Indonesia)
-   **Mata Pelajaran Referensi**: Bervariasi sesuai jurusan
-   **Kurikulum Spesifik**: Berbeda untuk Kurikulum Merdeka dan Kurikulum 2013

### **Error Handling:**

-   Semua endpoint mengembalikan response dengan format yang konsisten
-   Error code sesuai dengan HTTP status code standar
-   Message error dalam bahasa Indonesia

---

## 🚀 Next Steps

1. **Frontend Development**: Buat web TKA dengan Next.js
2. **Session Management**: Implementasi session/token untuk keamanan
3. **Validation**: Tambahan validasi di frontend
4. **UI/UX**: Design interface yang user-friendly
5. **Testing**: Unit testing dan integration testing
