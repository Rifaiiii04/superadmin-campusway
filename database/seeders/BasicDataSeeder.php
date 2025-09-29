<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BasicDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert basic data without description
        $rumpunIlmu = [
            ['name' => 'Humaniora', 'is_active' => true],
            ['name' => 'Ilmu Sosial', 'is_active' => true],
            ['name' => 'Ilmu Alam', 'is_active' => true],
            ['name' => 'Ilmu Formal', 'is_active' => true],
            ['name' => 'Ilmu Terapan', 'is_active' => true],
            ['name' => 'Ilmu Kesehatan', 'is_active' => true],
            ['name' => 'Ilmu Lingkungan', 'is_active' => true],
            ['name' => 'Ilmu Teknologi', 'is_active' => true],
        ];

        foreach ($rumpunIlmu as $rumpun) {
            DB::table('rumpun_ilmu')->updateOrInsert(
                ['name' => $rumpun['name']],
                $rumpun
            );
        }

        $programStudi = [
            ['name' => 'Seni', 'rumpun_ilmu_id' => 1, 'is_active' => true],
            ['name' => 'Sejarah', 'rumpun_ilmu_id' => 1, 'is_active' => true],
            ['name' => 'Linguistik', 'rumpun_ilmu_id' => 1, 'is_active' => true],
            ['name' => 'Sastra', 'rumpun_ilmu_id' => 1, 'is_active' => true],
            ['name' => 'Filsafat', 'rumpun_ilmu_id' => 1, 'is_active' => true],
            ['name' => 'Ekonomi', 'rumpun_ilmu_id' => 2, 'is_active' => true],
            ['name' => 'Psikologi', 'rumpun_ilmu_id' => 2, 'is_active' => true],
            ['name' => 'Sosiologi', 'rumpun_ilmu_id' => 2, 'is_active' => true],
            ['name' => 'Kimia', 'rumpun_ilmu_id' => 3, 'is_active' => true],
            ['name' => 'Fisika', 'rumpun_ilmu_id' => 3, 'is_active' => true],
            ['name' => 'Biologi', 'rumpun_ilmu_id' => 3, 'is_active' => true],
            ['name' => 'Matematika', 'rumpun_ilmu_id' => 4, 'is_active' => true],
            ['name' => 'Komputer', 'rumpun_ilmu_id' => 4, 'is_active' => true],
            ['name' => 'Kedokteran', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Farmasi', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Manajemen', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Teknik Sipil', 'rumpun_ilmu_id' => 5, 'is_active' => true],
        ];

        foreach ($programStudi as $prodi) {
            DB::table('program_studi')->updateOrInsert(
                ['name' => $prodi['name']],
                $prodi
            );
        }

        $subjects = [
            ['name' => 'Bahasa Indonesia', 'code' => 'BIN', 'is_required' => true, 'is_active' => true, 'education_level' => 'Umum', 'subject_type' => 'Wajib', 'subject_number' => 1],
            ['name' => 'Matematika', 'code' => 'MTK', 'is_required' => true, 'is_active' => true, 'education_level' => 'Umum', 'subject_type' => 'Wajib', 'subject_number' => 2],
            ['name' => 'Bahasa Inggris', 'code' => 'BIG', 'is_required' => true, 'is_active' => true, 'education_level' => 'Umum', 'subject_type' => 'Wajib', 'subject_number' => 3],
            ['name' => 'Fisika', 'code' => 'FIS', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 4],
            ['name' => 'Kimia', 'code' => 'KIM', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 5],
            ['name' => 'Biologi', 'code' => 'BIO', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 6],
            ['name' => 'Ekonomi', 'code' => 'EKO', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 7],
            ['name' => 'Sosiologi', 'code' => 'SOS', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 8],
            ['name' => 'Geografi', 'code' => 'GEO', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 9],
            ['name' => 'Sejarah', 'code' => 'SEJ', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 10],
            ['name' => 'Antropologi', 'code' => 'ANT', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 11],
            ['name' => 'PPKn', 'code' => 'PPKN', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 12],
            ['name' => 'Bahasa Arab', 'code' => 'BAR', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 13],
            ['name' => 'Bahasa Jerman', 'code' => 'BJE', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 14],
            ['name' => 'Bahasa Prancis', 'code' => 'BPR', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 15],
            ['name' => 'Bahasa Jepang', 'code' => 'BJP', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 16],
            ['name' => 'Bahasa Korea', 'code' => 'BKO', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 17],
            ['name' => 'Bahasa Mandarin', 'code' => 'BMA', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 18],
            ['name' => 'Produk Kreatif dan Kewirausahaan', 'code' => 'PKK', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMK/MAK', 'subject_type' => 'Produk_Kreatif_Kewirausahaan', 'subject_number' => 19],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['name' => $subject['name']],
                $subject
            );
        }

        $majorRecommendations = [
            [
                'major_name' => 'Kedokteran',
                'min_score' => 80.00,
                'max_score' => 100.00,
                'category' => 'Saintek',
                'required_subjects' => json_encode(['Biologi', 'Kimia', 'Fisika']),
                'preferred_subjects' => json_encode(['Matematika', 'Bahasa Inggris']),
                'career_prospects' => 'Dokter, Spesialis, Peneliti Medis',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Biologi', 'Kimia', 'Fisika', 'Matematika Tingkat Lanjut']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Biologi', 'Kimia', 'Fisika', 'Matematika']),
                'kurikulum_2013_ips_subjects' => json_encode(['Biologi', 'Kimia', 'Fisika', 'Matematika']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Biologi', 'Kimia', 'Fisika', 'Matematika']),
            ],
            [
                'major_name' => 'Teknik Sipil',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Saintek',
                'required_subjects' => json_encode(['Matematika', 'Fisika']),
                'preferred_subjects' => json_encode(['Kimia', 'Bahasa Inggris']),
                'career_prospects' => 'Insinyur Sipil, Konsultan Konstruksi',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Matematika Tingkat Lanjut', 'Fisika', 'Kimia']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Matematika', 'Fisika', 'Kimia']),
                'kurikulum_2013_ips_subjects' => json_encode(['Matematika', 'Fisika', 'Kimia']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Matematika', 'Fisika', 'Kimia']),
            ],
            [
                'major_name' => 'Ekonomi',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Soshum',
                'required_subjects' => json_encode(['Matematika', 'Ekonomi']),
                'preferred_subjects' => json_encode(['Bahasa Inggris', 'Sosiologi']),
                'career_prospects' => 'Ekonom, Analis Keuangan, Konsultan',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Ekonomi', 'Matematika Tingkat Lanjut', 'Sosiologi']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Ekonomi', 'Matematika', 'Sosiologi']),
                'kurikulum_2013_ips_subjects' => json_encode(['Ekonomi', 'Matematika', 'Sosiologi']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Ekonomi', 'Matematika', 'Sosiologi']),
            ],
            [
                'major_name' => 'Manajemen',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Soshum',
                'required_subjects' => json_encode(['Matematika', 'Ekonomi']),
                'preferred_subjects' => json_encode(['Bahasa Inggris', 'Sosiologi']),
                'career_prospects' => 'Manager, Konsultan, Entrepreneur',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Ekonomi', 'Matematika Tingkat Lanjut', 'Sosiologi']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Ekonomi', 'Matematika', 'Sosiologi']),
                'kurikulum_2013_ips_subjects' => json_encode(['Ekonomi', 'Matematika', 'Sosiologi']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Ekonomi', 'Matematika', 'Sosiologi']),
            ],
            [
                'major_name' => 'Psikologi',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Soshum',
                'required_subjects' => json_encode(['Bahasa Indonesia', 'Sosiologi']),
                'preferred_subjects' => json_encode(['Matematika', 'Biologi']),
                'career_prospects' => 'Psikolog, Konselor, Peneliti',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Sosiologi', 'Matematika Tingkat Lanjut', 'Pendidikan Pancasila']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Sosiologi', 'Matematika', 'PPKn']),
                'kurikulum_2013_ips_subjects' => json_encode(['Sosiologi', 'Matematika', 'PPKn']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Sosiologi', 'Matematika', 'PPKn']),
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
