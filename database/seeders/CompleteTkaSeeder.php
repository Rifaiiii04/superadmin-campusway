<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompleteTkaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert Rumpun Ilmu dengan ID eksplisit
        $rumpunIlmu = [
            ['id' => 1, 'name' => 'Humaniora', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Ilmu Sosial', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Ilmu Alam', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Ilmu Formal', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Ilmu Terapan', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Ilmu Kesehatan', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'Ilmu Lingkungan', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => 'Ilmu Teknologi', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($rumpunIlmu as $rumpun) {
            DB::table('rumpun_ilmu')->updateOrInsert(
                ['id' => $rumpun['id']],
                $rumpun
            );
        }

        // Insert Program Studi
        $programStudi = [
            ['name' => 'Seni', 'rumpun_ilmu_id' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sejarah', 'rumpun_ilmu_id' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Linguistik', 'rumpun_ilmu_id' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sastra', 'rumpun_ilmu_id' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Filsafat', 'rumpun_ilmu_id' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ekonomi', 'rumpun_ilmu_id' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Psikologi', 'rumpun_ilmu_id' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sosiologi', 'rumpun_ilmu_id' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kimia', 'rumpun_ilmu_id' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fisika', 'rumpun_ilmu_id' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Biologi', 'rumpun_ilmu_id' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Matematika', 'rumpun_ilmu_id' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Komputer', 'rumpun_ilmu_id' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kedokteran', 'rumpun_ilmu_id' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Farmasi', 'rumpun_ilmu_id' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manajemen', 'rumpun_ilmu_id' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Teknik Sipil', 'rumpun_ilmu_id' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($programStudi as $prodi) {
            DB::table('program_studi')->updateOrInsert(
                ['name' => $prodi['name']],
                $prodi
            );
        }

        // Insert Subjects sesuai Kepmendikdasmen No. 95/M/2025
        $subjects = [
            // Mata pelajaran wajib (3) - untuk kelas 12 SMA/MA/SMK
            ['name' => 'Bahasa Indonesia', 'code' => 'BIN', 'is_required' => true, 'is_active' => true, 'education_level' => 'Umum', 'subject_type' => 'Wajib', 'subject_number' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Matematika', 'code' => 'MTK', 'is_required' => true, 'is_active' => true, 'education_level' => 'Umum', 'subject_type' => 'Wajib', 'subject_number' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bahasa Inggris', 'code' => 'BIG', 'is_required' => true, 'is_active' => true, 'education_level' => 'Umum', 'subject_type' => 'Wajib', 'subject_number' => 0, 'created_at' => now(), 'updated_at' => now()],
            
            // Mata pelajaran pilihan (19 mata pelajaran) - nomor 1-19
            ['name' => 'Matematika Lanjutan', 'code' => 'MTK_L', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bahasa Indonesia Lanjutan', 'code' => 'BIN_L', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bahasa Inggris Lanjutan', 'code' => 'BIG_L', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fisika', 'code' => 'FIS', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kimia', 'code' => 'KIM', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Biologi', 'code' => 'BIO', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ekonomi', 'code' => 'EKO', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sosiologi', 'code' => 'SOS', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Geografi', 'code' => 'GEO', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sejarah', 'code' => 'SEJ', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Antropologi', 'code' => 'ANT', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PPKn', 'code' => 'PPKN', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bahasa Arab', 'code' => 'BAR', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bahasa Jerman', 'code' => 'BJE', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 14, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bahasa Prancis', 'code' => 'BPR', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bahasa Jepang', 'code' => 'BJP', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 16, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bahasa Korea', 'code' => 'BKO', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 17, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bahasa Mandarin', 'code' => 'BMA', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 18, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Produk Kreatif dan Kewirausahaan', 'code' => 'PKK', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMK/MAK', 'subject_type' => 'Produk_Kreatif_Kewirausahaan', 'subject_number' => 19, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['name' => $subject['name']],
                $subject
            );
        }

        // Insert Major Recommendations minimal
        $majorRecommendations = [
            [
                'major_name' => 'Kedokteran',
                'category' => 'Saintek',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Teknik Sipil',
                'category' => 'Saintek',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ekonomi',
                'category' => 'Soshum',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Manajemen',
                'category' => 'Soshum',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Psikologi',
                'category' => 'Soshum',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($majorRecommendations as $major) {
            DB::table('major_recommendations')->updateOrInsert(
                ['major_name' => $major['major_name']],
                $major
            );
        }
    }
}
