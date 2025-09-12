# 🎯 **SISTEM JSON UNTUK MATA PELAJARAN SMK**

## 📋 **OVERVIEW**

Sistem JSON untuk mata pelajaran SMK telah dibuat untuk memberikan fleksibilitas dalam mengelola mata pelajaran pilihan SMK sesuai dengan regulasi. Sistem ini memungkinkan konfigurasi yang mudah dan dapat diubah tanpa perlu mengubah kode.

## 🏗️ **STRUKTUR SISTEM**

### 1. **File Konfigurasi JSON**

-   **File**: `config/smk_subjects.json`
-   **Fungsi**: Menyimpan konfigurasi mata pelajaran SMK
-   **Fleksibilitas**: Mudah diubah tanpa mengubah kode

### 2. **Helper Class**

-   **File**: `app/Helpers/SMKSubjectHelper.php`
-   **Fungsi**: Mengelola logika mata pelajaran SMK
-   **Method**: Static methods untuk berbagai operasi

### 3. **Command Update**

-   **File**: `app/Console/Commands/UpdateSMKMappingCommand.php`
-   **Fungsi**: Mengupdate mapping SMK berdasarkan JSON
-   **Command**: `php artisan mapping:update-smk`

## 📊 **STRUKTUR JSON**

```json
{
    "smk_subjects": {
        "mandatory": ["Bahasa Indonesia", "Bahasa Inggris", "Matematika"],
        "pkk_subject": "Produk/Projek Kreatif dan Kewirausahaan",
        "optional_subjects": [
            "Matematika Lanjutan",
            "Bahasa Indonesia Lanjutan",
            "Bahasa Inggris Lanjutan",
            "Fisika",
            "Kimia",
            "Biologi",
            "Ekonomi",
            "Sosiologi",
            "PPKn/Pendidikan Pancasila",
            "Sejarah",
            "Geografi",
            "Antropologi",
            "Seni Budaya",
            "Bahasa Prancis",
            "Bahasa Jepang",
            "Bahasa Korea",
            "Bahasa Mandarin",
            "Bahasa Arab",
            "Bahasa Jerman"
        ],
        "mapping_rules": {
            "default": {
                "pkk_priority": 1,
                "optional_priority": 2,
                "total_subjects": 2
            },
            "special_cases": {
                "Seni": {
                    "pkk_priority": 1,
                    "additional_subjects": ["Matematika"],
                    "total_subjects": 2
                },
                "Linguistik": {
                    "pkk_priority": 1,
                    "additional_subjects": ["Bahasa Indonesia Lanjutan"],
                    "total_subjects": 2
                },
                "Filsafat": {
                    "pkk_priority": 1,
                    "additional_subjects": ["Sosiologi"],
                    "total_subjects": 2
                },
                "Sejarah": {
                    "pkk_priority": 1,
                    "additional_subjects": ["Matematika"],
                    "total_subjects": 2
                },
                "Sastra": {
                    "pkk_priority": 1,
                    "additional_subjects": ["Matematika"],
                    "total_subjects": 2
                }
            }
        }
    }
}
```

## 🔧 **HELPER CLASS METHODS**

### **SMKSubjectHelper**

```php
// Get mandatory subjects for SMK
SMKSubjectHelper::getMandatorySubjects()

// Get PKK subject for SMK
SMKSubjectHelper::getPKKSubject()

// Get available optional subjects for SMK
SMKSubjectHelper::getOptionalSubjects()

// Get subjects for specific SMK major
SMKSubjectHelper::getSubjectsForMajor($majorName)

// Check if major is SMK
SMKSubjectHelper::isSMK($rumpunIlmu)

// Get all SMK subjects configuration
SMKSubjectHelper::getAllConfig()
```

## 🎯 **ATURAN YANG DITERAPKAN**

### **SMK/MAK (1 PKK + 1 pilihan)**

-   ✅ **PKK Subject**: Produk/Projek Kreatif dan Kewirausahaan (wajib)
-   ✅ **Optional Subject**: 1 mata pelajaran pilihan sesuai jurusan
-   ✅ **Total**: 2 mata pelajaran pilihan
-   ✅ **Konfigurasi**: Berdasarkan JSON, mudah diubah

### **SMA/MA (2 mata pelajaran pilihan)**

-   ✅ **Mapping**: Berdasarkan referensi tabel kurikulum Merdeka dan 2013
-   ✅ **Jumlah**: 2 mata pelajaran pilihan
-   ✅ **Relevansi**: Mata pelajaran relevan dengan jurusan

## 🚀 **CARA MENGGUNAKAN**

### **1. Update Mapping SMK**

```bash
php artisan mapping:update-smk
```

### **2. Clear Cache**

```bash
php artisan cache:clear-all
```

### **3. Restart Server**

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

## 📝 **CARA MENGUBAH KONFIGURASI**

### **1. Edit File JSON**

```bash
# Edit file konfigurasi
nano config/smk_subjects.json
```

### **2. Tambah Jurusan Baru**

```json
"special_cases": {
  "Jurusan Baru": {
    "pkk_priority": 1,
    "additional_subjects": ["Mata Pelajaran 1", "Mata Pelajaran 2"],
    "total_subjects": 2
  }
}
```

### **3. Update Mapping**

```bash
php artisan mapping:update-smk
```

## 📊 **HASIL IMPLEMENTASI**

### **SMK Majors dengan JSON Configuration**

-   ✅ **Seni**: Produk/Projek Kreatif dan Kewirausahaan, Matematika
-   ✅ **Linguistik**: Produk/Projek Kreatif dan Kewirausahaan, Bahasa Indonesia Lanjutan
-   ✅ **Filsafat**: Produk/Projek Kreatif dan Kewirausahaan, Sosiologi
-   ✅ **Sejarah**: Produk/Projek Kreatif dan Kewirausahaan, Matematika
-   ✅ **Sastra**: Produk/Projek Kreatif dan Kewirausahaan, Matematika

### **Database Statistics**

```
📊 Overall Statistics:
   SMA/MA Mappings: 107
   SMK/MAK Mappings: 10
```

## ✅ **KEUNTUNGAN SISTEM JSON**

### **1. Fleksibilitas**

-   ✅ **Mudah diubah**: Konfigurasi di JSON, bukan di kode
-   ✅ **Tanpa restart**: Perubahan langsung berlaku
-   ✅ **Version control**: Perubahan dapat di-track

### **2. Maintainability**

-   ✅ **Terpusat**: Semua konfigurasi SMK di satu tempat
-   ✅ **Konsisten**: Menggunakan helper class yang sama
-   ✅ **Testable**: Mudah di-test dan di-debug

### **3. Scalability**

-   ✅ **Tambah jurusan**: Mudah menambah jurusan SMK baru
-   ✅ **Ubah aturan**: Mudah mengubah aturan mapping
-   ✅ **Extensible**: Dapat dikembangkan lebih lanjut

## 🎉 **KESIMPULAN**

Sistem JSON untuk mata pelajaran SMK telah berhasil diimplementasikan:

-   ✅ **Konfigurasi JSON**: Fleksibel dan mudah diubah
-   ✅ **Helper Class**: Mengelola logika SMK dengan baik
-   ✅ **Command Update**: Otomatis mengupdate mapping
-   ✅ **Controller Integration**: Terintegrasi dengan controller
-   ✅ **Database Consistency**: Data konsisten di database

**Sistem siap digunakan dengan konfigurasi yang fleksibel!** 🎉
