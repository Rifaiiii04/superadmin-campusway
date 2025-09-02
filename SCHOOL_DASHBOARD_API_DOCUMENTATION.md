# School Dashboard API Documentation

API untuk dashboard sekolah yang memungkinkan sekolah untuk melihat data siswa dan jurusan yang diminati siswa mereka.

## Base URL

```
http://127.0.0.1:8000/api/school
```

## Authentication

Sekolah login menggunakan NPSN (8 digit) dan password. Setelah login berhasil, akan mendapatkan token yang harus digunakan untuk mengakses endpoint yang memerlukan autentikasi.

### Default Login Credentials (untuk testing)

| NPSN     | Nama Sekolah          | Password    |
| -------- | --------------------- | ----------- |
| 12345678 | SMA Negeri 1 Jakarta  | password123 |
| 87654321 | SMA Negeri 2 Bandung  | password123 |
| 11223344 | SMA Negeri 3 Surabaya | password123 |

### Token Format

Token adalah base64 encoded string dengan format: `school_id|timestamp|npsn`

### Token Expiry

Token berlaku selama 24 jam dari waktu login.

## Endpoints

### 1. Login Sekolah

**POST** `/login`

Login sekolah menggunakan NPSN dan password.

**Request Body:**

```json
{
    "npsn": "12345678",
    "password": "password123"
}
```

**Response Success (200):**

```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "token": "NHwxNzU2ODA1MTI2fDEyMzQ1Njc4",
        "school": {
            "id": 4,
            "npsn": "12345678",
            "name": "SMA Negeri 1 Jakarta"
        }
    }
}
```

**Response Error (404):**

```json
{
    "success": false,
    "message": "NPSN tidak ditemukan"
}
```

**Response Error (401):**

```json
{
    "success": false,
    "message": "Password salah"
}
```

### 2. Logout Sekolah

**POST** `/logout`

Logout sekolah (menghapus session).

**Headers:**

```
Authorization: <token>
```

**Response Success (200):**

```json
{
    "success": true,
    "message": "Logout berhasil"
}
```

### 3. Profile Sekolah

**GET** `/profile`

Mendapatkan informasi profile sekolah yang sedang login.

**Headers:**

```
Authorization: <token>
```

**Response Success (200):**

```json
{
    "success": true,
    "data": {
        "id": 4,
        "npsn": "12345678",
        "name": "SMA Negeri 1 Jakarta",
        "created_at": "2025-09-01T07:19:29.420000Z",
        "updated_at": "2025-09-01T07:19:29.420000Z"
    }
}
```

### 4. Dashboard Overview

**GET** `/dashboard`

Mendapatkan data overview dashboard sekolah termasuk statistik siswa dan jurusan yang diminati.

**Headers:**

```
Authorization: <token>
```

**Response Success (200):**

```json
{
    "success": true,
    "data": {
        "school": {
            "id": 4,
            "npsn": "12345678",
            "name": "SMA Negeri 1 Jakarta"
        },
        "statistics": {
            "total_students": 2,
            "students_with_choice": 2,
            "students_without_choice": 0,
            "completion_percentage": 100.0
        },
        "top_majors": [
            {
                "major_id": "22",
                "major_name": "Seni",
                "category": "Soshum",
                "student_count": 1
            },
            {
                "major_id": "23",
                "major_name": "Teknik Informatika",
                "category": "Saintek",
                "student_count": 1
            }
        ],
        "students_by_class": [
            {
                "kelas": "XII IPA 1",
                "student_count": 2
            }
        ]
    }
}
```

### 5. Daftar Semua Siswa

**GET** `/students`

Mendapatkan daftar semua siswa di sekolah beserta informasi jurusan yang dipilih.

**Headers:**

```
Authorization: <token>
```

**Response Success (200):**

```json
{
    "success": true,
    "data": {
        "school": {
            "id": 4,
            "npsn": "12345678",
            "name": "SMA Negeri 1 Jakarta"
        },
        "students": [
            {
                "id": 9,
                "nisn": "1234567890",
                "name": "John Doe",
                "class": "XII IPA 1",
                "email": "john@example.com",
                "phone": "081234567890",
                "parent_phone": "081234567891",
                "has_choice": true,
                "chosen_major": {
                    "id": 22,
                    "name": "Seni",
                    "category": "Soshum"
                },
                "choice_date": "2025-09-01T07:20:00.000000Z"
            },
            {
                "id": 10,
                "nisn": "0987654321",
                "name": "Jane Smith",
                "class": "XII IPA 1",
                "email": "jane@example.com",
                "phone": "081234567891",
                "parent_phone": "081234567892",
                "has_choice": true,
                "chosen_major": {
                    "id": 23,
                    "name": "Teknik Informatika",
                    "category": "Saintek"
                },
                "choice_date": "2025-09-01T07:21:00.000000Z"
            }
        ],
        "total_students": 2
    }
}
```

### 6. Detail Siswa

**GET** `/students/{studentId}`

Mendapatkan detail informasi siswa tertentu.

**Headers:**

```
Authorization: <token>
```

**Response Success (200):**

```json
{
    "success": true,
    "data": {
        "school": {
            "id": 4,
            "npsn": "12345678",
            "name": "SMA Negeri 1 Jakarta"
        },
        "student": {
            "id": 9,
            "nisn": "1234567890",
            "name": "John Doe",
            "class": "XII IPA 1",
            "email": "john@example.com",
            "phone": "081234567890",
            "parent_phone": "081234567891",
            "created_at": "2025-09-01T07:19:30.000000Z",
            "updated_at": "2025-09-01T07:19:30.000000Z",
            "has_choice": true,
            "chosen_major": {
                "id": 22,
                "name": "Seni",
                "description": "Program studi yang mempelajari seni dan kreativitas...",
                "career_prospects": "Seniman, Desainer, Kurator...",
                "category": "Soshum",
                "choice_date": "2025-09-01T07:20:00.000000Z"
            }
        }
    }
}
```

**Response Error (404):**

```json
{
    "success": false,
    "message": "Siswa tidak ditemukan"
}
```

### 7. Statistik Jurusan

**GET** `/major-statistics`

Mendapatkan statistik jurusan yang diminati siswa dengan persentase.

**Headers:**

```
Authorization: <token>
```

**Response Success (200):**

```json
{
    "success": true,
    "data": {
        "school": {
            "id": 4,
            "npsn": "12345678",
            "name": "SMA Negeri 1 Jakarta"
        },
        "total_students_with_choice": 2,
        "major_statistics": [
            {
                "major_id": "22",
                "major_name": "Seni",
                "description": "Program studi yang mempelajari seni dan kreativitas...",
                "category": "Soshum",
                "student_count": 1,
                "percentage": 50.0
            },
            {
                "major_id": "23",
                "major_name": "Teknik Informatika",
                "description": "Program studi yang mempelajari teknologi komputer...",
                "category": "Saintek",
                "student_count": 1,
                "percentage": 50.0
            }
        ]
    }
}
```

### 8. Siswa yang Belum Memilih Jurusan

**GET** `/students-without-choice`

Mendapatkan daftar siswa yang belum memilih jurusan.

**Headers:**

```
Authorization: <token>
```

**Response Success (200):**

```json
{
    "success": true,
    "data": {
        "school": {
            "id": 4,
            "npsn": "12345678",
            "name": "SMA Negeri 1 Jakarta"
        },
        "students_without_choice": [],
        "total_count": 0
    }
}
```

## Error Responses

### 401 Unauthorized

```json
{
    "success": false,
    "message": "Token tidak ditemukan"
}
```

### 401 Token Expired

```json
{
    "success": false,
    "message": "Token sudah expired"
}
```

### 404 Not Found

```json
{
    "success": false,
    "message": "Sekolah tidak ditemukan"
}
```

### 422 Validation Error

```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "npsn": ["NPSN harus 8 digit"],
        "password": ["Password minimal 6 karakter"]
    }
}
```

### 500 Server Error

```json
{
    "success": false,
    "message": "Terjadi kesalahan server"
}
```

## Usage Examples

### Login dan Akses Dashboard

```bash
# 1. Login
curl -X POST http://127.0.0.1:8000/api/school/login \
  -H "Content-Type: application/json" \
  -d '{"npsn": "12345678", "password": "password123"}'

# 2. Gunakan token untuk akses dashboard
curl -X GET http://127.0.0.1:8000/api/school/dashboard \
  -H "Authorization: NHwxNzU2ODA1MTI2fDEyMzQ1Njc4"
```

### JavaScript/Fetch Example

```javascript
// Login
const loginResponse = await fetch("http://127.0.0.1:8000/api/school/login", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
    },
    body: JSON.stringify({
        npsn: "12345678",
        password: "password123",
    }),
});

const loginData = await loginResponse.json();
const token = loginData.data.token;

// Akses dashboard
const dashboardResponse = await fetch(
    "http://127.0.0.1:8000/api/school/dashboard",
    {
        headers: {
            Authorization: token,
        },
    }
);

const dashboardData = await dashboardResponse.json();
console.log(dashboardData);
```

## Notes

1. **NPSN Format**: NPSN harus berupa string 8 digit
2. **Password**: Password minimal 6 karakter
3. **Token**: Token harus disertakan di header `Authorization` untuk endpoint yang memerlukan autentikasi
4. **Data Siswa**: Semua data siswa yang ditampilkan hanya untuk sekolah yang sedang login
5. **Jurusan**: Data jurusan diambil dari tabel `major_recommendations`
6. **Kelas**: Field kelas di database menggunakan nama `kelas`, bukan `class`

## Database Tables Used

-   `schools`: Data sekolah
-   `students`: Data siswa
-   `student_choices`: Pilihan jurusan siswa
-   `major_recommendations`: Data jurusan/rekomendasi
