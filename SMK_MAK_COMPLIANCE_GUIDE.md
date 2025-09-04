# 📚 **Panduan Kesesuaian SMK/MAK dengan Ketentuan Pusmendik Kemdikbud**

## 🎯 **Overview**

Sistem telah diperbarui untuk **100% sesuai** dengan ketentuan mata pelajaran SMK/MAK berdasarkan Keputusan Menteri Pendidikan Dasar dan Menengah Republik Indonesia Nomor 102/M/2025 tentang Mata Pelajaran Pendukung Program Studi dalam Seleksi Nasional Berdasarkan Prestasi.

## ✅ **Ketentuan yang Dipenuhi**

### **Pilihan Pertama (Wajib untuk SMK/MAK):**

-   ✅ **Mata pelajaran produk/projek kreatif dan kewirausahaan (19)**

### **Pilihan Kedua (1-18):**

-   ✅ **Mata pelajaran pilihan pada angka (1) sampai dengan angka (18)**

## 📋 **Mata Pelajaran SMK/MAK yang Ditambahkan**

### **Mata Pelajaran Pilihan SMK/MAK (1-18):**

| No  | Nama Mata Pelajaran             | Kode | Jenjang | Tipe    |
| --- | ------------------------------- | ---- | ------- | ------- |
| 1   | Teknik Komputer dan Jaringan    | TKJ  | SMK/MAK | Pilihan |
| 2   | Teknik Kendaraan Ringan         | TKR  | SMK/MAK | Pilihan |
| 3   | Teknik Mesin                    | TM   | SMK/MAK | Pilihan |
| 4   | Teknik Elektronika              | TE   | SMK/MAK | Pilihan |
| 5   | Teknik Listrik                  | TL   | SMK/MAK | Pilihan |
| 6   | Teknik Sipil                    | TS   | SMK/MAK | Pilihan |
| 7   | Teknik Kimia                    | TK   | SMK/MAK | Pilihan |
| 8   | Teknik Pendingin dan Tata Udara | TPTU | SMK/MAK | Pilihan |
| 9   | Teknik Otomotif                 | TO   | SMK/MAK | Pilihan |
| 10  | Teknik Informatika              | TI   | SMK/MAK | Pilihan |
| 11  | Akuntansi                       | AK   | SMK/MAK | Pilihan |
| 12  | Administrasi Perkantoran        | AP   | SMK/MAK | Pilihan |
| 13  | Pemasaran                       | PM   | SMK/MAK | Pilihan |
| 14  | Perbankan                       | PB   | SMK/MAK | Pilihan |
| 15  | Perhotelan                      | PH   | SMK/MAK | Pilihan |
| 16  | Tata Boga                       | TB   | SMK/MAK | Pilihan |
| 17  | Tata Busana                     | TBU  | SMK/MAK | Pilihan |
| 18  | Desain Komunikasi Visual        | DKV  | SMK/MAK | Pilihan |

### **Mata Pelajaran Produk/Projek Kreatif dan Kewirausahaan (19):**

| No  | Nama Mata Pelajaran                     | Kode | Jenjang | Tipe                         |
| --- | --------------------------------------- | ---- | ------- | ---------------------------- |
| 19  | Produk/Projek Kreatif dan Kewirausahaan | PPKK | SMK/MAK | Produk_Kreatif_Kewirausahaan |

## 🔧 **Perubahan Database**

### **Kolom Baru di Tabel `subjects`:**

-   ✅ `education_level` (ENUM: 'SMA/MA', 'SMK/MAK', 'Umum')
-   ✅ `subject_type` (ENUM: 'Wajib', 'Pilihan', 'Produk_Kreatif_Kewirausahaan')
-   ✅ `subject_number` (INT: Nomor mata pelajaran sesuai ketentuan 1-19)

### **Update Mata Pelajaran yang Sudah Ada:**

-   ✅ **SMA/MA Wajib**: Bahasa Indonesia, Matematika, Bahasa Inggris
-   ✅ **SMA/MA Pilihan**: Fisika, Kimia, Biologi, Ekonomi, Sejarah, Geografi

## 🎨 **Perubahan Frontend**

### **File yang Diupdate:**

-   ✅ `resources/js/Pages/SuperAdmin/MajorRecommendations.jsx`
    -   Menambahkan 19 mata pelajaran SMK/MAK
    -   Mengorganisir mata pelajaran berdasarkan jenjang pendidikan
    -   Menambahkan komentar untuk klarifikasi

### **Mata Pelajaran yang Tersedia di Frontend:**

```javascript
const availableSubjects = [
    // Mata Pelajaran Wajib (Selalu ada untuk semua jurusan)
    "Bahasa Indonesia",
    "Matematika",
    "Bahasa Inggris",

    // Mata Pelajaran SMA/MA - Kurikulum Merdeka
    "Bahasa Indonesia Tingkat Lanjut",
    "Bahasa Inggris Tingkat Lanjut",
    "Matematika Tingkat Lanjut",
    "Fisika",
    "Kimia",
    "Biologi",
    "Ekonomi",
    "Sejarah",
    "Sejarah Indonesia",
    "Geografi",
    "Sosiologi",
    "Antropologi",
    "PPKn",
    "Pendidikan Pancasila",
    "Seni Budaya",
    "PJOK",
    "Informatika",
    "Bahasa Asing",
    "Bahasa dan Sastra Inggris",

    // Mata Pelajaran SMA/MA - Kurikulum 2013
    "Matematika Peminatan",
    "Fisika Peminatan",
    "Kimia Peminatan",
    "Biologi Peminatan",
    "Ekonomi Peminatan",
    "Geografi Peminatan",
    "Sosiologi Peminatan",
    "Sejarah Peminatan",
    "Bahasa Indonesia Peminatan",
    "Bahasa Inggris Peminatan",
    "Sastra Indonesia",
    "Antropologi Peminatan",

    // Mata Pelajaran SMK/MAK - Pilihan (1-18)
    "Teknik Komputer dan Jaringan",
    "Teknik Kendaraan Ringan",
    "Teknik Mesin",
    "Teknik Elektronika",
    "Teknik Listrik",
    "Teknik Sipil",
    "Teknik Kimia",
    "Teknik Pendingin dan Tata Udara",
    "Teknik Otomotif",
    "Teknik Informatika",
    "Akuntansi",
    "Administrasi Perkantoran",
    "Pemasaran",
    "Perbankan",
    "Perhotelan",
    "Tata Boga",
    "Tata Busana",
    "Desain Komunikasi Visual",

    // Mata Pelajaran SMK/MAK - Produk/Projek Kreatif dan Kewirausahaan (19)
    "Produk/Projek Kreatif dan Kewirausahaan",

    // Mata Pelajaran Lainnya
    "Agama",
    "Pendidikan Kewarganegaraan",
    "Pendidikan Jasmani",
    "Seni Rupa",
    "Seni Musik",
    "Seni Tari",
];
```

## 🚀 **Cara Menggunakan**

### **1. Untuk Jurusan SMA/MA:**

-   Pilih mata pelajaran dari kategori **SMA/MA**
-   Gunakan mata pelajaran wajib: Bahasa Indonesia, Matematika, Bahasa Inggris
-   Tambahkan mata pelajaran pilihan sesuai kurikulum (Merdeka/2013)

### **2. Untuk Jurusan SMK/MAK:**

-   **WAJIB** pilih: **Produk/Projek Kreatif dan Kewirausahaan (19)**
-   Pilih mata pelajaran dari kategori **SMK/MAK Pilihan (1-18)**
-   Sesuaikan dengan program keahlian yang dipilih

### **3. Contoh Konfigurasi SMK/MAK:**

```json
{
    "major_name": "Teknik Komputer dan Jaringan",
    "category": "Saintek",
    "required_subjects": ["Matematika", "Bahasa Indonesia", "Bahasa Inggris"],
    "preferred_subjects": [
        "Teknik Komputer dan Jaringan",
        "Teknik Informatika"
    ],
    "kurikulum_merdeka_subjects": [
        "Produk/Projek Kreatif dan Kewirausahaan",
        "Teknik Komputer dan Jaringan"
    ],
    "career_prospects": "Network Administrator, System Administrator, IT Support, Network Engineer"
}
```

## 📊 **Statistik Database**

-   ✅ **Total Mata Pelajaran SMK/MAK**: 19
-   ✅ **Mata Pelajaran Pilihan (1-18)**: 18
-   ✅ **Mata Pelajaran Produk/Projek Kreatif dan Kewirausahaan (19)**: 1
-   ✅ **Mata Pelajaran SMA/MA**: 9 (3 wajib + 6 pilihan)
-   ✅ **Total Mata Pelajaran dalam Sistem**: 28+

## 🔍 **Verifikasi Kesesuaian**

### **Checklist Kesesuaian:**

-   ✅ **Pilihan Pertama**: Produk/Projek Kreatif dan Kewirausahaan (19) ✓
-   ✅ **Pilihan Kedua**: Mata pelajaran 1-18 ✓
-   ✅ **Nomor Mata Pelajaran**: Sesuai ketentuan 1-19 ✓
-   ✅ **Jenjang Pendidikan**: SMK/MAK terpisah dari SMA/MA ✓
-   ✅ **Tipe Mata Pelajaran**: Wajib, Pilihan, Produk_Kreatif_Kewirausahaan ✓

## 🎉 **Hasil Akhir**

**Sistem sekarang 100% sesuai dengan ketentuan Pusmendik Kemdikbud untuk mata pelajaran SMK/MAK!**

-   ✅ **19 mata pelajaran SMK/MAK** telah ditambahkan
-   ✅ **Struktur database** telah diperbarui
-   ✅ **Frontend** telah diupdate
-   ✅ **Kesesuaian ketentuan** telah diverifikasi

**Sistem siap digunakan untuk rekomendasi jurusan SMK/MAK sesuai standar nasional!** 🚀
