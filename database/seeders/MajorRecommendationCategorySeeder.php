<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MajorRecommendation;

class MajorRecommendationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update kategori untuk jurusan Saintek
        $saintekMajors = [
            'Teknik', 'Informatika', 'Kedokteran', 'Sipil', 'Mesin', 'Elektro', 'Arsitektur',
            'Fisika', 'Kimia', 'Biologi', 'Matematika', 'Statistika', 'Astronomi', 'Geologi',
            'Pertambangan', 'Perikanan', 'Peternakan', 'Kehutanan', 'Teknologi', 'Rekayasa',
            'Farmasi', 'Kesehatan', 'Gizi', 'Keperawatan', 'Kebidanan', 'Fisioterapi',
            'Biofisika', 'Biokimia', 'Biomedis', 'Teknik Kimia', 'Teknik Industri',
            'Teknik Lingkungan', 'Teknik Geologi', 'Teknik Geofisika', 'Teknik Nuklir',
            'Teknik Material', 'Teknik Metalurgi', 'Teknik Perminyakan', 'Teknik Geodesi'
        ];

        foreach ($saintekMajors as $keyword) {
            MajorRecommendation::where('major_name', 'LIKE', "%{$keyword}%")
                ->update(['category' => 'Saintek']);
        }

        // Update kategori untuk jurusan Soshum
        $soshumMajors = [
            'Akuntansi', 'Manajemen', 'Ekonomi', 'Sosiologi', 'Antropologi', 'Psikologi',
            'Ilmu Politik', 'Hubungan Internasional', 'Ilmu Komunikasi', 'Jurnalistik',
            'Periklanan', 'Penyiaran', 'Public Relations', 'Ilmu Hukum', 'Kriminologi',
            'Ilmu Administrasi', 'Ilmu Pemerintahan', 'Ilmu Sejarah', 'Arkeologi',
            'Filsafat', 'Ilmu Perpustakaan', 'Ilmu Informasi', 'Kesejahteraan Sosial',
            'Pendidikan', 'Bimbingan Konseling', 'Pendidikan Guru', 'Pendidikan Anak',
            'Pendidikan Luar Biasa', 'Pendidikan Jasmani', 'Pendidikan Seni',
            'Bahasa', 'Sastra', 'Linguistik', 'Pariwisata', 'Perhotelan', 'Kuliner'
        ];

        foreach ($soshumMajors as $keyword) {
            MajorRecommendation::where('major_name', 'LIKE', "%{$keyword}%")
                ->update(['category' => 'Soshum']);
        }

        // Update kategori untuk jurusan Campuran (yang membutuhkan keduanya)
        $campuranMajors = [
            'Psikologi Klinis', 'Psikologi Industri', 'Psikologi Pendidikan',
            'Ilmu Komputer', 'Sistem Informasi', 'Teknologi Informasi',
            'Desain Komunikasi Visual', 'Desain Interior', 'Desain Produk',
            'Arsitektur Lanskap', 'Perencanaan Wilayah', 'Perencanaan Kota',
            'Ilmu Lingkungan', 'Kesehatan Masyarakat', 'Epidemiologi',
            'Biostatistika', 'Bioinformatika', 'Teknologi Pangan',
            'Agribisnis', 'Agroteknologi', 'Ilmu Tanaman', 'Ilmu Tanah'
        ];

        foreach ($campuranMajors as $keyword) {
            MajorRecommendation::where('major_name', 'LIKE', "%{$keyword}%")
                ->update(['category' => 'Campuran']);
        }

        // Set default untuk yang belum terupdate
        MajorRecommendation::whereNull('category')
            ->orWhere('category', '')
            ->update(['category' => 'Saintek']);

        $this->command->info('Major recommendation categories updated successfully!');
    }
}
