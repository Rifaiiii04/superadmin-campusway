<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MajorRecommendationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing major recommendations
        DB::table('major_recommendations')->truncate();

        $majorRecommendations = [
            // RUMPUN ILMU HUMANIORA
            [
                'major_name' => 'Seni',
                'description' => 'Program studi yang mempelajari tentang seni dan budaya',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Soshum',
                'required_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris']),
                'preferred_subjects' => json_encode(['Seni Budaya', 'Sejarah']),
                'career_prospects' => 'Seniman, Kurator, Kritikus Seni, Desainer, Pengajar Seni',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Seni Budaya', 'Sejarah', 'Bahasa Indonesia Tingkat Lanjut', 'Bahasa Inggris Tingkat Lanjut']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Seni Budaya', 'Sejarah', 'Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_ips_subjects' => json_encode(['Seni Budaya', 'Sejarah', 'Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Seni Budaya', 'Sejarah', 'Bahasa Indonesia', 'Bahasa Inggris']),
            ],
            [
                'major_name' => 'Sejarah',
                'description' => 'Program studi yang mempelajari tentang peristiwa masa lalu',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Soshum',
                'required_subjects' => json_encode(['Bahasa Indonesia', 'Sejarah']),
                'preferred_subjects' => json_encode(['Bahasa Inggris', 'Geografi']),
                'career_prospects' => 'Sejarawan, Peneliti, Kurator Museum, Penulis, Pengajar',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Sejarah', 'Bahasa Indonesia Tingkat Lanjut', 'Bahasa Inggris Tingkat Lanjut']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Sejarah', 'Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_ips_subjects' => json_encode(['Sejarah', 'Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Sejarah', 'Bahasa Indonesia', 'Bahasa Inggris']),
            ],
            [
                'major_name' => 'Linguistik',
                'description' => 'Program studi yang mempelajari tentang bahasa dan struktur bahasa',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Soshum',
                'required_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris']),
                'preferred_subjects' => json_encode(['Bahasa Asing', 'Sosiologi']),
                'career_prospects' => 'Linguis, Penerjemah, Editor, Peneliti Bahasa, Pengajar',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Bahasa Indonesia Tingkat Lanjut', 'Bahasa Inggris Tingkat Lanjut', 'Bahasa Asing']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris', 'Bahasa Asing']),
                'kurikulum_2013_ips_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris', 'Bahasa Asing']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris', 'Bahasa Asing']),
            ],

            // RUMPUN ILMU SOSIAL
            [
                'major_name' => 'Ekonomi',
                'description' => 'Program studi yang mempelajari tentang ekonomi dan keuangan',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Soshum',
                'required_subjects' => json_encode(['Matematika', 'Ekonomi']),
                'preferred_subjects' => json_encode(['Bahasa Inggris', 'Sosiologi']),
                'career_prospects' => 'Ekonom, Analis Keuangan, Konsultan, Bankir, Pengajar',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Ekonomi', 'Matematika Tingkat Lanjut', 'Sosiologi']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Ekonomi', 'Matematika', 'Sosiologi']),
                'kurikulum_2013_ips_subjects' => json_encode(['Ekonomi', 'Matematika', 'Sosiologi']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Ekonomi', 'Matematika', 'Sosiologi']),
            ],
            [
                'major_name' => 'Psikologi',
                'description' => 'Program studi yang mempelajari tentang perilaku dan mental manusia',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Soshum',
                'required_subjects' => json_encode(['Bahasa Indonesia', 'Sosiologi']),
                'preferred_subjects' => json_encode(['Matematika', 'Biologi']),
                'career_prospects' => 'Psikolog, Konselor, Peneliti, HRD, Pengajar',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Sosiologi', 'Matematika Tingkat Lanjut', 'Pendidikan Pancasila']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Sosiologi', 'Matematika', 'PPKn']),
                'kurikulum_2013_ips_subjects' => json_encode(['Sosiologi', 'Matematika', 'PPKn']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Sosiologi', 'Matematika', 'PPKn']),
            ],

            // RUMPUN ILMU ALAM
            [
                'major_name' => 'Kimia',
                'description' => 'Program studi yang mempelajari tentang kimia dan reaksi kimia',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Saintek',
                'required_subjects' => json_encode(['Kimia', 'Matematika']),
                'preferred_subjects' => json_encode(['Fisika', 'Biologi']),
                'career_prospects' => 'Kimiawan, Peneliti, Analis Kimia, Quality Control, Pengajar',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Kimia', 'Fisika', 'Matematika Tingkat Lanjut']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Kimia', 'Fisika', 'Matematika']),
                'kurikulum_2013_ips_subjects' => json_encode(['Kimia', 'Fisika', 'Matematika']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Kimia', 'Fisika', 'Matematika']),
            ],
            [
                'major_name' => 'Fisika',
                'description' => 'Program studi yang mempelajari tentang fisika dan hukum alam',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Saintek',
                'required_subjects' => json_encode(['Fisika', 'Matematika']),
                'preferred_subjects' => json_encode(['Kimia', 'Bahasa Inggris']),
                'career_prospects' => 'Fisikawan, Peneliti, Engineer, Konsultan, Pengajar',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Fisika', 'Matematika Tingkat Lanjut', 'Kimia']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Fisika', 'Matematika', 'Kimia']),
                'kurikulum_2013_ips_subjects' => json_encode(['Fisika', 'Matematika', 'Kimia']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Fisika', 'Matematika', 'Kimia']),
            ],
            [
                'major_name' => 'Biologi',
                'description' => 'Program studi yang mempelajari tentang makhluk hidup',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Saintek',
                'required_subjects' => json_encode(['Biologi', 'Kimia']),
                'preferred_subjects' => json_encode(['Fisika', 'Matematika']),
                'career_prospects' => 'Biolog, Peneliti, Konservasionis, Quality Control, Pengajar',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Biologi', 'Kimia', 'Fisika']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Biologi', 'Kimia', 'Fisika']),
                'kurikulum_2013_ips_subjects' => json_encode(['Biologi', 'Kimia', 'Fisika']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Biologi', 'Kimia', 'Fisika']),
            ],

            // RUMPUN ILMU FORMAL
            [
                'major_name' => 'Matematika',
                'description' => 'Program studi yang mempelajari tentang matematika dan perhitungan',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Saintek',
                'required_subjects' => json_encode(['Matematika', 'Fisika']),
                'preferred_subjects' => json_encode(['Kimia', 'Bahasa Inggris']),
                'career_prospects' => 'Matematikawan, Peneliti, Aktuaris, Analis Data, Pengajar',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Matematika Tingkat Lanjut', 'Fisika', 'Kimia']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Matematika', 'Fisika', 'Kimia']),
                'kurikulum_2013_ips_subjects' => json_encode(['Matematika', 'Fisika', 'Kimia']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Matematika', 'Fisika', 'Kimia']),
            ],
            [
                'major_name' => 'Komputer',
                'description' => 'Program studi yang mempelajari tentang ilmu komputer dan teknologi informasi',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Saintek',
                'required_subjects' => json_encode(['Matematika', 'Fisika']),
                'preferred_subjects' => json_encode(['Bahasa Inggris', 'Kimia']),
                'career_prospects' => 'Programmer, Software Engineer, Data Scientist, System Analyst, Pengajar',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Matematika Tingkat Lanjut', 'Fisika', 'Kimia']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Matematika', 'Fisika', 'Kimia']),
                'kurikulum_2013_ips_subjects' => json_encode(['Matematika', 'Fisika', 'Kimia']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Matematika', 'Fisika', 'Kimia']),
            ],

            // RUMPUN ILMU KESEHATAN
            [
                'major_name' => 'Kedokteran',
                'description' => 'Program studi yang mempelajari tentang kedokteran dan kesehatan',
                'min_score' => 80.00,
                'max_score' => 100.00,
                'category' => 'Saintek',
                'required_subjects' => json_encode(['Biologi', 'Kimia', 'Fisika']),
                'preferred_subjects' => json_encode(['Matematika', 'Bahasa Inggris']),
                'career_prospects' => 'Dokter, Spesialis, Peneliti Medis, Konsultan Kesehatan, Pengajar',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Biologi', 'Kimia', 'Fisika', 'Matematika Tingkat Lanjut']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Biologi', 'Kimia', 'Fisika', 'Matematika']),
                'kurikulum_2013_ips_subjects' => json_encode(['Biologi', 'Kimia', 'Fisika', 'Matematika']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Biologi', 'Kimia', 'Fisika', 'Matematika']),
            ],
            [
                'major_name' => 'Farmasi',
                'description' => 'Program studi yang mempelajari tentang obat-obatan dan farmasi',
                'min_score' => 70.00,
                'max_score' => 100.00,
                'category' => 'Saintek',
                'required_subjects' => json_encode(['Kimia', 'Biologi']),
                'preferred_subjects' => json_encode(['Matematika', 'Fisika']),
                'career_prospects' => 'Apoteker, Peneliti Obat, Quality Control, Konsultan Farmasi, Pengajar',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Kimia', 'Biologi', 'Matematika Tingkat Lanjut']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Kimia', 'Biologi', 'Matematika']),
                'kurikulum_2013_ips_subjects' => json_encode(['Kimia', 'Biologi', 'Matematika']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Kimia', 'Biologi', 'Matematika']),
            ],

            // RUMPUN ILMU TERAPAN
            [
                'major_name' => 'Teknik Sipil',
                'description' => 'Program studi yang mempelajari tentang konstruksi dan infrastruktur',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Saintek',
                'required_subjects' => json_encode(['Matematika', 'Fisika']),
                'preferred_subjects' => json_encode(['Kimia', 'Bahasa Inggris']),
                'career_prospects' => 'Insinyur Sipil, Konsultan Konstruksi, Project Manager, Pengajar',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Matematika Tingkat Lanjut', 'Fisika', 'Kimia']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Matematika', 'Fisika', 'Kimia']),
                'kurikulum_2013_ips_subjects' => json_encode(['Matematika', 'Fisika', 'Kimia']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Matematika', 'Fisika', 'Kimia']),
            ],
            [
                'major_name' => 'Manajemen',
                'description' => 'Program studi yang mempelajari tentang manajemen dan administrasi',
                'min_score' => 60.00,
                'max_score' => 100.00,
                'category' => 'Soshum',
                'required_subjects' => json_encode(['Matematika', 'Ekonomi']),
                'preferred_subjects' => json_encode(['Bahasa Inggris', 'Sosiologi']),
                'career_prospects' => 'Manager, Konsultan, Entrepreneur, HRD, Pengajar',
                'is_active' => true,
                'kurikulum_merdeka_subjects' => json_encode(['Ekonomi', 'Matematika Tingkat Lanjut', 'Sosiologi']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Ekonomi', 'Matematika', 'Sosiologi']),
                'kurikulum_2013_ips_subjects' => json_encode(['Ekonomi', 'Matematika', 'Sosiologi']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Ekonomi', 'Matematika', 'Sosiologi']),
            ],
        ];

        foreach ($majorRecommendations as $major) {
            DB::table('major_recommendations')->insert($major);
        }
    }
}