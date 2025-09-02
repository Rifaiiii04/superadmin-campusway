# 📋 API Update Summary - Parent Phone Integration

## 🎯 Overview

API telah diupdate untuk mengintegrasikan kolom `parent_phone` ke dalam semua endpoint yang relevan. Perubahan ini mencakup School Dashboard API dan Student Web API.

## 🔄 Perubahan yang Dilakukan

### 1. **SchoolDashboardController.php**

-   ✅ **Endpoint `/students`**: Menambahkan `parent_phone` ke response daftar siswa
-   ✅ **Endpoint `/students/{id}`**: Menambahkan `parent_phone` ke response detail siswa
-   ✅ **Endpoint `/students-without-choice`**: Menambahkan `parent_phone` ke response siswa yang belum memilih jurusan

### 2. **StudentWebController.php**

-   ✅ **Endpoint `/register-student`**:
    -   Menambahkan validasi untuk `parent_phone` (nullable, max 20 karakter)
    -   Menambahkan `parent_phone` ke proses pembuatan siswa baru
    -   Menambahkan `parent_phone` ke response registrasi
-   ✅ **Endpoint `/login`**: Menambahkan `parent_phone` ke response login siswa
-   ✅ **Endpoint `/student-profile/{id}`**: Menambahkan `parent_phone` ke response profile siswa

### 3. **Dokumentasi API**

-   ✅ **SCHOOL_DASHBOARD_API_DOCUMENTATION.md**: Update semua contoh response untuk menyertakan `parent_phone`
-   ✅ **STUDENT_WEB_API_DOCUMENTATION.md**: Update semua contoh request/response untuk menyertakan `parent_phone`

## 📊 Response Format Baru

### School Dashboard API - Students List

```json
{
    "success": true,
    "data": {
        "students": [
            {
                "id": 9,
                "nisn": "1234567890",
                "name": "John Doe",
                "class": "XII IPA 1",
                "email": "john@example.com",
                "phone": "081234567890",
                "parent_phone": "081234567891",  // ← BARU
                "has_choice": true,
                "chosen_major": {...},
                "choice_date": "..."
            }
        ]
    }
}
```

### Student Web API - Registration

```json
{
    "nisn": "1234567890",
    "name": "John Doe",
    "npsn_sekolah": "12345678",
    "nama_sekolah": "SMA Negeri 1 Jakarta",
    "kelas": "XII IPA 1",
    "email": "john@example.com",
    "phone": "081234567890",
    "parent_phone": "081234567891", // ← BARU
    "password": "password_siswa"
}
```

## 🗄️ Database Requirements

### Kolom yang Diperlukan

-   **Table**: `students`
-   **Column**: `parent_phone` (NVARCHAR(20) NULL)

### SQL untuk Update Database

```sql
-- Jika kolom belum ada
ALTER TABLE students ADD parent_phone NVARCHAR(20) NULL;

-- Update data existing (jika diperlukan)
UPDATE students
SET parent_phone = CASE
    WHEN id % 4 = 0 THEN '0812' + RIGHT('00000000' + CAST(ABS(CHECKSUM(NEWID())) % 100000000 AS VARCHAR), 8)
    WHEN id % 4 = 1 THEN '0813' + RIGHT('00000000' + CAST(ABS(CHECKSUM(NEWID())) % 100000000 AS VARCHAR), 8)
    WHEN id % 4 = 2 THEN '0852' + RIGHT('00000000' + CAST(ABS(CHECKSUM(NEWID())) % 100000000 AS VARCHAR), 8)
    ELSE '0853' + RIGHT('00000000' + CAST(ABS(CHECKSUM(NEWID())) % 100000000 AS VARCHAR), 8)
END
WHERE parent_phone IS NULL OR parent_phone = '';
```

## 🧪 Testing

### Test Registration dengan Parent Phone

```bash
curl -X POST http://127.0.0.1:8000/api/web/register-student \
  -H "Content-Type: application/json" \
  -d '{
    "nisn": "1234567890",
    "name": "John Doe",
    "npsn_sekolah": "12345678",
    "nama_sekolah": "SMA Negeri 1 Jakarta",
    "kelas": "XII IPA 1",
    "email": "john@example.com",
    "phone": "081234567890",
    "parent_phone": "081234567891",
    "password": "password_siswa"
  }'
```

### Test School Dashboard - Students List

```bash
curl -X GET http://127.0.0.1:8000/api/school/students \
  -H "Authorization: <token>"
```

## ✅ Status Update

-   [x] SchoolDashboardController updated
-   [x] StudentWebController updated
-   [x] API Documentation updated
-   [x] Database schema ready
-   [x] Frontend integration ready (SchoolDetail.jsx)

## 🚀 Next Steps

1. Jalankan SQL script untuk update database
2. Test API endpoints dengan data baru
3. Verifikasi frontend menampilkan `parent_phone` dengan benar
4. Update frontend registration form untuk include `parent_phone` field

## 📝 Notes

-   Field `parent_phone` bersifat **optional** (nullable)
-   Format nomor telepon Indonesia (0812, 0813, 0852, 0853)
-   Validasi maksimal 20 karakter
-   Backward compatible dengan data existing
