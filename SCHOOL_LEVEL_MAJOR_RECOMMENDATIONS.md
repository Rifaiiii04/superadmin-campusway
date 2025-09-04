# 🎓 School Level Major Recommendations System

## 📋 **Overview**

Sistem pengkondisian major recommendations berdasarkan jenjang sekolah (SMK/MAK vs SMA/MA) yang sesuai dengan ketentuan Pusmendik Kemdikbud.

## 🎯 **Fitur Utama**

### **1. Pengkondisian Berdasarkan Jenjang Sekolah**

-   **SMK/MAK**: Menggunakan mata pelajaran sesuai ketentuan Pusmendik (1-18 pilihan + produk kreatif kewirausahaan)
-   **SMA/MA**: Menggunakan mata pelajaran umum sesuai kurikulum

### **2. API Endpoints Baru**

-   `GET /api/school-level/majors?school_level=SMA/MA|SMK/MAK`
-   `GET /api/school-level/subjects?school_level=SMA/MA|SMK/MAK`
-   `GET /api/school-level/stats`

### **3. Database Schema Updates**

-   Tabel `schools` ditambahkan kolom `school_level` (ENUM: 'SMA/MA', 'SMK/MAK')
-   Tabel `subjects` sudah memiliki `education_level` dan `subject_type`

## 🔧 **Implementasi**

### **Backend Changes**

#### **1. Migration**

```php
// database/migrations/2025_09_04_062704_add_school_level_to_schools_table.php
Schema::table('schools', function (Blueprint $table) {
    $table->enum('school_level', ['SMA/MA', 'SMK/MAK'])->default('SMA/MA')->after('name');
});
```

#### **2. Model Updates**

```php
// app/Models/School.php
protected $fillable = [
    'npsn',
    'name',
    'school_level', // New
    'password_hash',
];
```

#### **3. Controller Baru**

```php
// app/Http/Controllers/SchoolLevelMajorController.php
class SchoolLevelMajorController extends Controller
{
    public function getMajorsBySchoolLevel(Request $request)
    public function getSubjectsBySchoolLevel(Request $request)
    public function getSchoolLevelStats()
}
```

#### **4. Seeder**

```php
// database/seeders/SchoolLevelMajorRecommendationsSeeder.php
- Update major recommendations untuk SMK/MAK dengan mata pelajaran sesuai ketentuan
- Update major recommendations untuk SMA/MA dengan mata pelajaran umum
```

### **Frontend Changes**

#### **1. API Service**

```typescript
// src/services/api.ts
export const schoolLevelApiService = {
  getMajorsBySchoolLevel(schoolLevel: 'SMA/MA' | 'SMK/MAK')
  getSubjectsBySchoolLevel(schoolLevel: 'SMA/MA' | 'SMK/MAK')
  getSchoolLevelStats()
}
```

#### **2. Komponen Baru**

```typescript
// src/components/SchoolLevelMajorSelector.tsx
- School level selector (SMA/MA vs SMK/MAK)
- Statistics display
- Subjects list based on school level
- Majors list with filtered subjects
```

## 📊 **Data Structure**

### **SMK/MAK Subjects (Sesuai Ketentuan Pusmendik)**

#### **Mata Pelajaran Pilihan (1-18):**

1. Teknik Komputer dan Jaringan
2. Teknik Kendaraan Ringan
3. Teknik Mesin
4. Teknik Elektronika
5. Teknik Listrik
6. Teknik Sipil
7. Teknik Kimia
8. Teknik Pendingin dan Tata Udara
9. Teknik Otomotif
10. Teknik Informatika
11. Akuntansi
12. Administrasi Perkantoran
13. Pemasaran
14. Perbankan
15. Perhotelan
16. Tata Boga
17. Tata Busana
18. Desain Komunikasi Visual

#### **Mata Pelajaran Produk/Projek Kreatif dan Kewirausahaan (19):**

19. Produk/Projek Kreatif dan Kewirausahaan

### **SMA/MA Subjects (Mata Pelajaran Umum)**

-   Bahasa Indonesia
-   Matematika
-   Bahasa Inggris
-   Fisika
-   Kimia
-   Biologi
-   Ekonomi
-   Sejarah
-   Geografi
-   Sosiologi
-   PPKn
-   Seni Budaya
-   PJOK

## 🎨 **UI/UX Features**

### **1. School Level Selector**

-   Toggle button untuk memilih SMA/MA atau SMK/MAK
-   Visual feedback untuk pilihan aktif

### **2. Statistics Dashboard**

-   Total jurusan
-   Total mata pelajaran
-   Breakdown berdasarkan jenis mata pelajaran

### **3. Subjects Display**

-   Grid layout dengan card design
-   Color coding berdasarkan tipe mata pelajaran:
    -   **Wajib**: Merah
    -   **Pilihan**: Biru
    -   **Produk Kreatif**: Hijau

### **4. Majors Display**

-   Card layout dengan hover effects
-   Required subjects dan preferred subjects
-   Truncated display dengan "show more" functionality

## 🔄 **API Usage Examples**

### **Get Majors for SMK/MAK**

```javascript
const response = await schoolLevelApiService.getMajorsBySchoolLevel("SMK/MAK");
console.log(response.data); // Array of majors with SMK/MAK subjects
```

### **Get Subjects for SMA/MA**

```javascript
const response = await schoolLevelApiService.getSubjectsBySchoolLevel("SMA/MA");
console.log(response.data); // Array of SMA/MA subjects
```

### **Get Statistics**

```javascript
const response = await schoolLevelApiService.getSchoolLevelStats();
console.log(response.data.sma_ma); // SMA/MA statistics
console.log(response.data.smk_mak); // SMK/MAK statistics
```

## ✅ **Compliance dengan Ketentuan Pusmendik**

### **SMK/MAK Compliance:**

-   ✅ **Pilihan Pertama**: Mata pelajaran produk/projek kreatif dan kewirausahaan (19)
-   ✅ **Pilihan Kedua**: Mata pelajaran pilihan pada angka (1) sampai dengan angka (18)
-   ✅ **Total**: 19 mata pelajaran sesuai ketentuan

### **SMA/MA Compliance:**

-   ✅ **Mata Pelajaran Wajib**: Bahasa Indonesia, Matematika, Bahasa Inggris
-   ✅ **Mata Pelajaran Pilihan**: Sesuai kurikulum 2013 dan Kurikulum Merdeka
-   ✅ **Kategorisasi**: Saintek, Soshum, Seni, Pendidikan

## 🚀 **Cara Penggunaan**

### **1. Backend Setup**

```bash
# Run migration
php artisan migrate --path=database/migrations/2025_09_04_062704_add_school_level_to_schools_table.php

# Run seeder
php artisan db:seed --class=SchoolLevelMajorRecommendationsSeeder
```

### **2. Frontend Integration**

```typescript
import SchoolLevelMajorSelector from "@/components/SchoolLevelMajorSelector";

// Use in your component
<SchoolLevelMajorSelector
    onMajorSelect={(major) => console.log("Selected:", major)}
    selectedSchoolLevel="SMK/MAK"
/>;
```

### **3. API Testing**

```bash
# Test SMK/MAK majors
curl "http://127.0.0.1:8000/api/school-level/majors?school_level=SMK/MAK"

# Test SMA/MA subjects
curl "http://127.0.0.1:8000/api/school-level/subjects?school_level=SMA/MA"

# Test statistics
curl "http://127.0.0.1:8000/api/school-level/stats"
```

## 📈 **Benefits**

1. **Compliance**: 100% sesuai dengan ketentuan Pusmendik Kemdikbud
2. **Flexibility**: Mendukung kedua jenjang pendidikan
3. **Accuracy**: Rekomendasi jurusan yang lebih tepat berdasarkan jenjang
4. **User Experience**: Interface yang intuitif dan informatif
5. **Performance**: Caching dan optimisasi untuk performa yang baik

## 🔮 **Future Enhancements**

1. **Auto-detection**: Deteksi otomatis jenjang sekolah dari data siswa
2. **Advanced Filtering**: Filter berdasarkan kategori jurusan
3. **Recommendation Engine**: Algoritma rekomendasi yang lebih canggih
4. **Analytics**: Dashboard analitik untuk tracking pilihan siswa
5. **Integration**: Integrasi dengan sistem penilaian dan evaluasi

---

**Sistem ini memastikan bahwa rekomendasi jurusan disesuaikan dengan jenjang pendidikan dan ketentuan yang berlaku, memberikan pengalaman yang lebih personal dan akurat untuk setiap siswa.**
