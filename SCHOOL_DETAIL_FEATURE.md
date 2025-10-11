# Fitur Lihat Detail Sekolah

## Deskripsi
Fitur "Lihat Detail" pada halaman Kelola Sekolah memungkinkan Super Admin untuk melihat detail lengkap sekolah beserta daftar siswa yang terdaftar di sekolah tersebut.

## Fitur yang Tersedia

### 1. Informasi Sekolah
- Nama sekolah
- NPSN (Nomor Pokok Sekolah Nasional)
- Tanggal pendaftaran sekolah

### 2. Statistik Siswa
- Total jumlah siswa
- Jumlah siswa yang sudah memilih jurusan
- Jumlah siswa yang belum memilih jurusan

### 3. Daftar Siswa
Tabel yang menampilkan informasi lengkap setiap siswa:
- **Nama Siswa**: Nama lengkap dan NISN
- **Kelas**: Kelas siswa (contoh: XII IPA 1)
- **Kontak**: Email dan nomor telepon siswa
- **No. Orang Tua**: Nomor telepon orang tua
- **Status**: Status aktif/non-aktif siswa
- **Jurusan Pilihan**: Jurusan yang dipilih siswa (jika ada)
- **Tanggal Daftar**: Tanggal pendaftaran siswa

## Cara Menggunakan

1. Buka halaman **Kelola Sekolah** (`/schools`)
2. Klik tombol **"Lihat Detail"** pada sekolah yang ingin dilihat
3. Halaman detail akan menampilkan:
   - Informasi sekolah di bagian atas
   - Statistik siswa dalam bentuk kartu
   - Tabel daftar siswa di bagian bawah

## Teknologi yang Digunakan

### Backend (Laravel)
- **Controller**: `SchoolController@show`
- **Model**: `School` dengan relasi `students`
- **Route**: `GET /schools/{school}`

### Frontend (React + Inertia.js)
- **Component**: `SchoolDetail.jsx`
- **Layout**: `SuperAdminLayout`
- **Styling**: Tailwind CSS

## Struktur Data

### School Model
```php
protected $fillable = [
    'npsn',
    'name', 
    'school_level',
    'password',
];

public function students()
{
    return $this->hasMany(Student::class);
}
```

### Student Model
```php
protected $fillable = [
    'nisn',
    'name',
    'school_id',
    'kelas',
    'email',
    'phone',
    'parent_phone',
    'password',
    'status',
];

public function school()
{
    return $this->belongsTo(School::class);
}
```

## Perbaikan yang Dilakukan

1. **Controller**: Memperbaiki method `show` untuk memuat data siswa dengan relasi yang benar
2. **Frontend**: Memperbaiki tampilan tabel siswa dan statistik
3. **Data**: Menambahkan data siswa untuk testing
4. **Relasi**: Memastikan relasi antara School dan Student berfungsi dengan baik

## Testing

Untuk menguji fitur ini:
1. Pastikan server Laravel berjalan
2. Buka browser dan akses `/schools`
3. Klik "Lihat Detail" pada salah satu sekolah
4. Pastikan data siswa ditampilkan dengan benar

## Catatan

- Fitur ini menggunakan warna hijau (#16A34A) dan putih (#FFFFFF) sesuai preferensi UI
- Data siswa ditampilkan dalam format tabel yang responsif
- Jika tidak ada siswa, akan ditampilkan pesan "Belum ada siswa terdaftar di sekolah ini"
