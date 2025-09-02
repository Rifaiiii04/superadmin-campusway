# Panduan Login Dashboard Sekolah

## Informasi Login untuk Sekolah

Sistem dashboard sekolah telah dibuat dengan akun default untuk testing. Berikut adalah informasi login yang bisa digunakan:

### Akun Sekolah yang Tersedia:

| NPSN | Nama Sekolah | Password |
|------|--------------|----------|
| 12345678 | SMA Negeri 1 Jakarta | password123 |
| 87654321 | SMA Negeri 2 Bandung | password123 |
| 11223344 | SMA Negeri 3 Surabaya | password123 |

## Cara Login

### 1. Melalui API (Postman/Insomnia)

**Endpoint:** `POST http://127.0.0.1:8000/api/school/login`

**Request Body:**
```json
{
    "npsn": "12345678",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "token": "NHwxNzU2ODA2MDQ4fDEyMzQ1Njc4",
        "school": {
            "id": 4,
            "npsn": "12345678",
            "name": "SMA Negeri 1 Jakarta"
        }
    }
}
```

### 2. Melalui cURL

```bash
curl -X POST http://127.0.0.1:8000/api/school/login \
  -H "Content-Type: application/json" \
  -d '{"npsn": "12345678", "password": "password123"}'
```

### 3. Melalui JavaScript/Fetch

```javascript
const loginResponse = await fetch('http://127.0.0.1:8000/api/school/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        npsn: '12345678',
        password: 'password123'
    })
});

const loginData = await loginResponse.json();
console.log(loginData);
```

## Setelah Login Berhasil

Setelah login berhasil, Anda akan mendapatkan **token** yang harus digunakan untuk mengakses semua endpoint dashboard sekolah.

### Menggunakan Token

Token harus disertakan di header `Authorization` untuk setiap request ke endpoint yang memerlukan autentikasi.

**Contoh penggunaan token:**

```bash
curl -X GET http://127.0.0.1:8000/api/school/dashboard \
  -H "Authorization: NHwxNzU2ODA2MDQ4fDEyMzQ1Njc4"
```

## Endpoint yang Bisa Diakses Setelah Login

1. **Dashboard Overview** - `GET /api/school/dashboard`
2. **Daftar Siswa** - `GET /api/school/students`
3. **Detail Siswa** - `GET /api/school/students/{id}`
4. **Statistik Jurusan** - `GET /api/school/major-statistics`
5. **Siswa Belum Pilih Jurusan** - `GET /api/school/students-without-choice`
6. **Profile Sekolah** - `GET /api/school/profile`
7. **Logout** - `POST /api/school/logout`

## Fitur Dashboard Sekolah

Setelah login, sekolah dapat:

### 📊 Dashboard Overview
- Melihat total siswa
- Melihat berapa siswa yang sudah/belum memilih jurusan
- Melihat persentase completion
- Melihat top 5 jurusan yang paling diminati
- Melihat distribusi siswa per kelas

### 👥 Manajemen Data Siswa
- Melihat daftar semua siswa
- Melihat detail informasi siswa
- Melihat jurusan yang dipilih setiap siswa
- Melihat siswa yang belum memilih jurusan

### 📈 Statistik Jurusan
- Melihat statistik jurusan yang diminati
- Melihat persentase pemilihan setiap jurusan
- Melihat deskripsi dan prospek karir jurusan

## Catatan Penting

1. **Token Expiry**: Token berlaku selama 24 jam
2. **Data Isolation**: Setiap sekolah hanya bisa melihat data siswa mereka sendiri
3. **NPSN Format**: NPSN harus berupa string 8 digit
4. **Password**: Password minimal 6 karakter

## Troubleshooting

### Error "NPSN tidak ditemukan"
- Pastikan NPSN yang dimasukkan benar (8 digit)
- Pastikan sekolah sudah terdaftar di sistem

### Error "Password salah"
- Pastikan password yang dimasukkan benar
- Password default untuk testing: `password123`

### Error "Token tidak ditemukan" atau "Token sudah expired"
- Lakukan login ulang untuk mendapatkan token baru
- Token berlaku selama 24 jam

## Untuk Production

Untuk penggunaan production, sebaiknya:

1. **Ganti Password Default**: Ubah password default menjadi password yang lebih aman
2. **Buat Akun Baru**: Buat akun sekolah baru dengan NPSN dan password yang sesuai
3. **Implementasi JWT**: Pertimbangkan menggunakan JWT token untuk keamanan yang lebih baik
4. **Rate Limiting**: Implementasi rate limiting untuk mencegah brute force attack

## Membuat Akun Sekolah Baru

Untuk membuat akun sekolah baru, Anda bisa:

1. **Melalui Database**: Insert langsung ke tabel `schools`
2. **Melalui Seeder**: Buat seeder baru atau modifikasi `SchoolSeeder`
3. **Melalui Admin Panel**: Buat fitur admin untuk mengelola akun sekolah

### Contoh Insert Manual ke Database:

```sql
INSERT INTO schools (npsn, name, password_hash, created_at, updated_at) 
VALUES ('99887766', 'SMA Negeri 4 Yogyakarta', '$2y$10$...', GETDATE(), GETDATE());
```

**Note**: Password harus di-hash menggunakan Laravel Hash::make() atau bcrypt.
