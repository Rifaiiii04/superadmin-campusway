<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateMajorRecommendationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update major recommendations dengan mata pelajaran sesuai Kepmendikdasmen
        $updates = [
            // HUMANIORA
            'Seni' => [
                'required_subjects' => json_encode(['Seni Budaya']),
                'preferred_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris']),
                'career_prospects' => 'Seniman, Desainer, Kurator, Pengajar Seni',
                'kurikulum_merdeka_subjects' => json_encode(['Seni Budaya']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Seni Budaya']),
                'kurikulum_2013_ips_subjects' => json_encode(['Seni Budaya']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Seni Budaya']),
            ],
            'Sejarah' => [
                'required_subjects' => json_encode(['Sejarah']),
                'preferred_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris']),
                'career_prospects' => 'Sejarawan, Peneliti, Kurator Museum, Penulis',
                'kurikulum_merdeka_subjects' => json_encode(['Sejarah']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Sejarah Indonesia']),
                'kurikulum_2013_ips_subjects' => json_encode(['Sejarah Indonesia']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Sejarah Indonesia']),
            ],
            'Linguistik' => [
                'required_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris']),
                'preferred_subjects' => json_encode(['Bahasa Asing']),
                'career_prospects' => 'Linguis, Penerjemah, Editor, Peneliti Bahasa',
                'kurikulum_merdeka_subjects' => json_encode(['Bahasa Indonesia Tingkat Lanjut', 'Bahasa Inggris']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_ips_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris']),
            ],
            'Susastra atau Sastra' => [
                'required_subjects' => json_encode(['Bahasa Indonesia Tingkat Lanjut']),
                'preferred_subjects' => json_encode(['Bahasa Asing yang Relevan']),
                'career_prospects' => 'Sastrawan, Kritikus Sastra, Editor, Penulis',
                'kurikulum_merdeka_subjects' => json_encode(['Bahasa Indonesia Tingkat Lanjut', 'Bahasa Asing yang Relevan']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Asing yang Relevan']),
                'kurikulum_2013_ips_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Asing yang Relevan']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Asing yang Relevan']),
            ],
            'Filsafat' => [
                'required_subjects' => json_encode(['Sosiologi']),
                'preferred_subjects' => json_encode(['Bahasa Indonesia', 'Sejarah']),
                'career_prospects' => 'Filsuf, Peneliti, Dosen, Konsultan',
                'kurikulum_merdeka_subjects' => json_encode(['Sosiologi']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Sejarah Indonesia']),
                'kurikulum_2013_ips_subjects' => json_encode(['Sosiologi']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Antropologi']),
            ],
            
            // ILMU SOSIAL
            'Sosial' => [
                'required_subjects' => json_encode(['Sosiologi']),
                'preferred_subjects' => json_encode(['Bahasa Indonesia', 'Sejarah']),
                'career_prospects' => 'Sosiolog, Peneliti Sosial, Konsultan, Aktivis',
                'kurikulum_merdeka_subjects' => json_encode(['Sosiologi']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Sejarah Indonesia']),
                'kurikulum_2013_ips_subjects' => json_encode(['Sosiologi']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Antropologi']),
            ],
            'Ekonomi' => [
                'required_subjects' => json_encode(['Ekonomi', 'Matematika']),
                'preferred_subjects' => json_encode(['Bahasa Inggris', 'Sosiologi']),
                'career_prospects' => 'Ekonom, Analis Keuangan, Konsultan, Bankir',
                'kurikulum_merdeka_subjects' => json_encode(['Ekonomi', 'Matematika Tingkat Lanjut']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Matematika', 'Ekonomi']),
                'kurikulum_2013_ips_subjects' => json_encode(['Ekonomi', 'Matematika']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Matematika', 'Ekonomi']),
            ],
            'Pertahanan' => [
                'required_subjects' => json_encode(['Pendidikan Pancasila']),
                'preferred_subjects' => json_encode(['Bahasa Indonesia', 'Sejarah']),
                'career_prospects' => 'TNI, Polri, Analis Keamanan, Konsultan Pertahanan',
                'kurikulum_merdeka_subjects' => json_encode(['Pendidikan Pancasila']),
                'kurikulum_2013_ipa_subjects' => json_encode(['PPKn']),
                'kurikulum_2013_ips_subjects' => json_encode(['PPKn']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['PPKn']),
            ],
            'Psikologi' => [
                'required_subjects' => json_encode(['Sosiologi', 'Matematika']),
                'preferred_subjects' => json_encode(['Bahasa Indonesia', 'Biologi']),
                'career_prospects' => 'Psikolog, Konselor, Peneliti, HRD',
                'kurikulum_merdeka_subjects' => json_encode(['Sosiologi', 'Matematika Tingkat Lanjut', 'Pendidikan Pancasila']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Matematika', 'PPKn']),
                'kurikulum_2013_ips_subjects' => json_encode(['Sosiologi', 'PPKn']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Matematika', 'PPKn']),
            ],
            
            // ILMU ALAM
            'Kimia' => [
                'required_subjects' => json_encode(['Kimia']),
                'preferred_subjects' => json_encode(['Matematika', 'Fisika']),
                'career_prospects' => 'Ahli Kimia, Peneliti, Quality Control, Konsultan',
                'kurikulum_merdeka_subjects' => json_encode(['Kimia']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Kimia']),
                'kurikulum_2013_ips_subjects' => json_encode(['Kimia']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Kimia']),
            ],
            'Fisika' => [
                'required_subjects' => json_encode(['Fisika']),
                'preferred_subjects' => json_encode(['Matematika', 'Kimia']),
                'career_prospects' => 'Fisikawan, Peneliti, Engineer, Dosen',
                'kurikulum_merdeka_subjects' => json_encode(['Fisika']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Fisika']),
                'kurikulum_2013_ips_subjects' => json_encode(['Fisika']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Fisika']),
            ],
            'Biologi' => [
                'required_subjects' => json_encode(['Biologi']),
                'preferred_subjects' => json_encode(['Kimia', 'Matematika']),
                'career_prospects' => 'Biolog, Peneliti, Konservasionis, Dosen',
                'kurikulum_merdeka_subjects' => json_encode(['Biologi']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Biologi']),
                'kurikulum_2013_ips_subjects' => json_encode(['Biologi']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Biologi']),
            ],
            
            // ILMU KESEHATAN
            'Ilmu atau Sains Kedokteran' => [
                'required_subjects' => json_encode(['Biologi', 'Kimia']),
                'preferred_subjects' => json_encode(['Fisika', 'Matematika']),
                'career_prospects' => 'Dokter, Spesialis, Peneliti Medis, Dosen',
                'kurikulum_merdeka_subjects' => json_encode(['Biologi', 'Kimia']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Biologi', 'Kimia']),
                'kurikulum_2013_ips_subjects' => json_encode(['Biologi', 'Kimia']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Biologi', 'Kimia']),
            ],
            'Ilmu Farmasi' => [
                'required_subjects' => json_encode(['Biologi', 'Kimia']),
                'preferred_subjects' => json_encode(['Matematika', 'Fisika']),
                'career_prospects' => 'Apoteker, Peneliti Farmasi, Quality Control, Dosen',
                'kurikulum_merdeka_subjects' => json_encode(['Biologi', 'Kimia']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Biologi', 'Kimia']),
                'kurikulum_2013_ips_subjects' => json_encode(['Biologi', 'Kimia']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Biologi', 'Kimia']),
            ],
        ];

        foreach ($updates as $majorName => $data) {
            DB::table('major_recommendations')
                ->where('major_name', $majorName)
                ->update($data);
        }
    }
}