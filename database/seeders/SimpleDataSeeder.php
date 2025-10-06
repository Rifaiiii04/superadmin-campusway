<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data (disable foreign key checks)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('major_subject_mappings')->delete();
        DB::table('major_recommendations')->delete();
        DB::table('subjects')->delete();
        DB::table('program_studi')->delete();
        DB::table('rumpun_ilmu')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert Rumpun Ilmu
        $rumpunIlmu = [
            ['name' => 'Humaniora', 'description' => 'Manusia', 'is_active' => true],
            ['name' => 'Ilmu Sosial', 'description' => 'Masyarakat', 'is_active' => true],
            ['name' => 'Ilmu Alam', 'description' => 'Alam', 'is_active' => true],
            ['name' => 'Ilmu Formal', 'description' => 'Formal', 'is_active' => true],
            ['name' => 'Ilmu Terapan', 'description' => 'Terapan', 'is_active' => true],
            ['name' => 'Ilmu Kesehatan', 'description' => 'Kesehatan', 'is_active' => true],
            ['name' => 'Ilmu Lingkungan', 'description' => 'Lingkungan', 'is_active' => true],
            ['name' => 'Ilmu Teknologi', 'description' => 'Teknologi', 'is_active' => true],
        ];

        foreach ($rumpunIlmu as $rumpun) {
            DB::table('rumpun_ilmu')->insert($rumpun);
        }

        // Insert Program Studi
        $programStudi = [
            ['name' => 'Seni', 'description' => 'Seni', 'rumpun_ilmu_id' => 1, 'is_active' => true],
            ['name' => 'Sejarah', 'description' => 'Sejarah', 'rumpun_ilmu_id' => 1, 'is_active' => true],
            ['name' => 'Linguistik', 'description' => 'Bahasa', 'rumpun_ilmu_id' => 1, 'is_active' => true],
            ['name' => 'Sastra', 'description' => 'Sastra', 'rumpun_ilmu_id' => 1, 'is_active' => true],
            ['name' => 'Filsafat', 'description' => 'Filsafat', 'rumpun_ilmu_id' => 1, 'is_active' => true],
            ['name' => 'Ekonomi', 'description' => 'Ekonomi', 'rumpun_ilmu_id' => 2, 'is_active' => true],
            ['name' => 'Psikologi', 'description' => 'Psikologi', 'rumpun_ilmu_id' => 2, 'is_active' => true],
            ['name' => 'Sosiologi', 'description' => 'Sosiologi', 'rumpun_ilmu_id' => 2, 'is_active' => true],
            ['name' => 'Kimia', 'description' => 'Kimia', 'rumpun_ilmu_id' => 3, 'is_active' => true],
            ['name' => 'Fisika', 'description' => 'Fisika', 'rumpun_ilmu_id' => 3, 'is_active' => true],
            ['name' => 'Biologi', 'description' => 'Biologi', 'rumpun_ilmu_id' => 3, 'is_active' => true],
            ['name' => 'Matematika', 'description' => 'Matematika', 'rumpun_ilmu_id' => 4, 'is_active' => true],
            ['name' => 'Komputer', 'description' => 'Komputer', 'rumpun_ilmu_id' => 4, 'is_active' => true],
            ['name' => 'Kedokteran', 'description' => 'Kedokteran', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Farmasi', 'description' => 'Farmasi', 'rumpun_ilmu_id' => 6, 'is_active' => true],
            ['name' => 'Manajemen', 'description' => 'Manajemen', 'rumpun_ilmu_id' => 5, 'is_active' => true],
            ['name' => 'Teknik Sipil', 'description' => 'Teknik Sipil', 'rumpun_ilmu_id' => 5, 'is_active' => true],
        ];

        foreach ($programStudi as $prodi) {
            DB::table('program_studi')->insert($prodi);
        }

        // Insert Subjects
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
            DB::table('subjects')->insert($subject);
        }

        // Insert Major Recommendations
        $majorRecommendations = [
            [
                'major_name' => 'Kedokteran',
                'description' => 'Program studi kedokteran',
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
                'description' => 'Program studi teknik sipil',
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
                'description' => 'Program studi ekonomi',
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
                'description' => 'Program studi manajemen',
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
                'description' => 'Program studi psikologi',
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
            DB::table('major_recommendations')->insert($major);
        }
    }
}
