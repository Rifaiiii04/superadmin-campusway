<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramStudiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programStudi = [
            // RUMPUN ILMU HUMANIORA
            ['name' => 'Seni', 'description' => 'Studi tentang seni dan budaya', 'rumpun_ilmu_id' => 1, 'is_active' => true],
            ['name' => 'Sejarah', 'description' => 'Program studi yang mempelajari tentang peristiwa masa lalu', 'rumpun_ilmu_id' => 1, 'is_active' => true],
            ['name' => 'Linguistik', 'description' => 'Program studi yang mempelajari tentang bahasa dan struktur bahasa', 'rumpun_ilmu_id' => 1, 'is_active' => true],
            ['name' => 'Susastra', 'description' => 'Program studi yang mempelajari tentang sastra dan karya sastra', 'rumpun_ilmu_id' => 1, 'is_active' => true],
            ['name' => 'Sastra', 'description' => 'Program studi yang mempelajari tentang sastra dan bahasa', 'rumpun_ilmu_id' => 1, 'is_active' => true],
            ['name' => 'Filsafat', 'description' => 'Program studi yang mempelajari tentang pemikiran dan kebijaksanaan', 'rumpun_ilmu_id' => 1, 'is_active' => true],

            // RUMPUN ILMU SOSIAL
            ['name' => 'Sosial', 'description' => 'Program studi yang mempelajari tentang masyarakat dan interaksi sosial', 'rumpun_ilmu_id' => 2, 'is_active' => true],
            ['name' => 'Ekonomi', 'description' => 'Program studi yang mempelajari tentang ekonomi dan keuangan', 'rumpun_ilmu_id' => 2, 'is_active' => true],
            ['name' => 'Pertahanan', 'description' => 'Program studi yang mempelajari tentang pertahanan dan keamanan', 'rumpun_ilmu_id' => 2, 'is_active' => true],
            ['name' => 'Psikologi', 'description' => 'Program studi yang mempelajari tentang perilaku dan mental manusia', 'rumpun_ilmu_id' => 2, 'is_active' => true],

            // RUMPUN ILMU ALAM
            ['name' => 'Kimia', 'description' => 'Program studi yang mempelajari tentang kimia dan reaksi kimia', 'rumpun_ilmu_id' => 3, 'is_active' => true],
            ['name' => 'Ilmu Kebumian', 'description' => 'Program studi yang mempelajari tentang bumi dan geologi', 'rumpun_ilmu_id' => 3, 'is_active' => true],
            ['name' => 'Ilmu Kelautan', 'description' => 'Program studi yang mempelajari tentang laut dan ekosistem laut', 'rumpun_ilmu_id' => 3, 'is_active' => true],
            ['name' => 'Biologi', 'description' => 'Program studi yang mempelajari tentang makhluk hidup', 'rumpun_ilmu_id' => 3, 'is_active' => true],
            ['name' => 'Biofisika', 'description' => 'Program studi yang mempelajari tentang fisika dalam biologi', 'rumpun_ilmu_id' => 3, 'is_active' => true],
            ['name' => 'Fisika', 'description' => 'Program studi yang mempelajari tentang fisika dan hukum alam', 'rumpun_ilmu_id' => 3, 'is_active' => true],
            ['name' => 'Astronomi', 'description' => 'Program studi yang mempelajari tentang benda langit dan antariksa', 'rumpun_ilmu_id' => 3, 'is_active' => true],

            // RUMPUN ILMU FORMAL
            ['name' => 'Komputer', 'description' => 'Program studi yang mempelajari tentang ilmu komputer dan teknologi informasi', 'rumpun_ilmu_id' => 4, 'is_active' => true],
            ['name' => 'Logika', 'description' => 'Program studi yang mempelajari tentang logika dan penalaran', 'rumpun_ilmu_id' => 4, 'is_active' => true],
            ['name' => 'Matematika', 'description' => 'Program studi yang mempelajari tentang matematika dan perhitungan', 'rumpun_ilmu_id' => 4, 'is_active' => true],

            // RUMPUN ILMU TERAPAN
            ['name' => 'Ilmu Pertanian', 'description' => 'Program studi yang mempelajari tentang pertanian dan budidaya', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Peternakan', 'description' => 'Program studi yang mempelajari tentang peternakan dan hewan ternak', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Ilmu Perikanan', 'description' => 'Program studi yang mempelajari tentang perikanan dan budidaya ikan', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Arsitektur', 'description' => 'Program studi yang mempelajari tentang desain bangunan dan arsitektur', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Perencanaan Wilayah', 'description' => 'Program studi yang mempelajari tentang perencanaan tata ruang dan wilayah', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Desain', 'description' => 'Program studi yang mempelajari tentang desain dan kreativitas', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Akuntansi', 'description' => 'Program studi yang mempelajari tentang akuntansi dan keuangan', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Manajemen', 'description' => 'Program studi yang mempelajari tentang manajemen dan administrasi', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Logistik', 'description' => 'Program studi yang mempelajari tentang logistik dan distribusi', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Administrasi Bisnis', 'description' => 'Program studi yang mempelajari tentang administrasi bisnis', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Bisnis', 'description' => 'Program studi yang mempelajari tentang bisnis dan kewirausahaan', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Ilmu Komunikasi', 'description' => 'Program studi yang mempelajari tentang komunikasi dan media', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Pendidikan', 'description' => 'Program studi yang mempelajari tentang pendidikan dan pengajaran', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Teknik Rekayasa', 'description' => 'Program studi yang mempelajari tentang teknik dan rekayasa', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Ilmu Lingkungan', 'description' => 'Program studi yang mempelajari tentang lingkungan hidup', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Kehutanan', 'description' => 'Program studi yang mempelajari tentang kehutanan dan konservasi hutan', 'rumpun_ilmu_id' => 5, 'is_active' => true],

            // RUMPUN ILMU KESEHATAN
            ['name' => 'Kedokteran', 'description' => 'Program studi yang mempelajari tentang kedokteran dan kesehatan', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Kedokteran Gigi', 'description' => 'Program studi yang mempelajari tentang kedokteran gigi', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Veteriner', 'description' => 'Program studi yang mempelajari tentang kedokteran hewan', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Farmasi', 'description' => 'Program studi yang mempelajari tentang obat-obatan dan farmasi', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Gizi', 'description' => 'Program studi yang mempelajari tentang gizi dan nutrisi', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Kesehatan Masyarakat', 'description' => 'Program studi yang mempelajari tentang kesehatan masyarakat', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Kebidanan', 'description' => 'Program studi yang mempelajari tentang kebidanan dan persalinan', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Keperawatan', 'description' => 'Program studi yang mempelajari tentang keperawatan dan perawatan pasien', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Kesehatan', 'description' => 'Program studi yang mempelajari tentang kesehatan secara umum', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Ilmu Informasi', 'description' => 'Program studi yang mempelajari tentang informasi dan data', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Hukum', 'description' => 'Program studi yang mempelajari tentang hukum dan peraturan', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Ilmu Militer', 'description' => 'Program studi yang mempelajari tentang militer dan pertahanan', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Urusan Publik', 'description' => 'Program studi yang mempelajari tentang administrasi publik', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Ilmu Keolahragaan', 'description' => 'Program studi yang mempelajari tentang olahraga dan kebugaran', 'rumpun_ilmu_id' => 6, 'is_active' => true],

            // RUMPUN ILMU LINGKUNGAN
            ['name' => 'Pariwisata', 'description' => 'Program studi yang mempelajari tentang pariwisata dan perjalanan', 'rumpun_ilmu_id' => 7, 'is_active' => true],
            ['name' => 'Transportasi', 'description' => 'Program studi yang mempelajari tentang transportasi dan logistik', 'rumpun_ilmu_id' => 7, 'is_active' => true],
            ['name' => 'Bioteknologi', 'description' => 'Program studi yang mempelajari tentang bioteknologi dan rekayasa hayati', 'rumpun_ilmu_id' => 7, 'is_active' => true],
            ['name' => 'Bioinformatika', 'description' => 'Program studi yang mempelajari tentang bioinformatika dan komputasi biologi', 'rumpun_ilmu_id' => 7, 'is_active' => true],
            ['name' => 'Geografi', 'description' => 'Program studi yang mempelajari tentang geografi dan tata ruang', 'rumpun_ilmu_id' => 7, 'is_active' => true],
            ['name' => 'Informatika Medis', 'description' => 'Program studi yang mempelajari tentang informatika dalam bidang medis', 'rumpun_ilmu_id' => 7, 'is_active' => true],
            ['name' => 'Konservasi Biologi', 'description' => 'Program studi yang mempelajari tentang konservasi dan pelestarian', 'rumpun_ilmu_id' => 7, 'is_active' => true],
            ['name' => 'Teknologi Pangan', 'description' => 'Program studi yang mempelajari tentang teknologi pangan dan pengolahan makanan', 'rumpun_ilmu_id' => 7, 'is_active' => true],
            ['name' => 'Sains Data', 'description' => 'Program studi yang mempelajari tentang sains data dan analisis data', 'rumpun_ilmu_id' => 7, 'is_active' => true],
            ['name' => 'Sains Perkopian', 'description' => 'Program studi yang mempelajari tentang sains perkopian dan penelitian', 'rumpun_ilmu_id' => 7, 'is_active' => true],
            ['name' => 'Studi Humanitas', 'description' => 'Program studi yang mempelajari tentang humanitas dan kemanusiaan', 'rumpun_ilmu_id' => 7, 'is_active' => true],
        ];

        foreach ($programStudi as $prodi) {
            DB::table('program_studi')->updateOrInsert(
                ['name' => $prodi['name']],
                $prodi
            );
        }
    }
}