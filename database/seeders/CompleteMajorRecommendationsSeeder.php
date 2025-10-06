<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MajorRecommendation;

class CompleteMajorRecommendationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Updating ALL 64 major recommendations with comprehensive subject references...');

        // Update semua jurusan dengan mata pelajaran yang sesuai
        $this->updateAllMajors();

        $this->command->info('All major recommendations updated successfully!');
    }

    private function updateAllMajors()
    {
        $allMajors = [
            // SAINTEK - Teknik & Teknologi
            'Ilmu Komputer /  Informatika' => [
                'required' => ['Matematika', 'Fisika'],
                'preferred' => ['Matematika', 'Fisika', 'Bahasa Inggris'],
                'kurikulum_merdeka' => ['Matematika', 'Fisika', 'Bahasa Inggris', 'Informatika'],
                'kurikulum_2013_ipa' => ['Matematika', 'Fisika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Teknik Mesin' => [
                'required' => ['Matematika', 'Fisika'],
                'preferred' => ['Matematika', 'Fisika', 'Kimia'],
                'kurikulum_merdeka' => ['Matematika', 'Fisika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Matematika', 'Fisika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Matematika', 'Fisika', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Matematika', 'Fisika', 'Bahasa Indonesia']
            ],
            'Teknik Elektro' => [
                'required' => ['Matematika', 'Fisika'],
                'preferred' => ['Matematika', 'Fisika', 'Kimia'],
                'kurikulum_merdeka' => ['Matematika', 'Fisika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Matematika', 'Fisika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Matematika', 'Fisika', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Matematika', 'Fisika', 'Bahasa Indonesia']
            ],
            'Arsitektur' => [
                'required' => ['Matematika', 'Fisika'],
                'preferred' => ['Matematika', 'Fisika', 'Seni Budaya'],
                'kurikulum_merdeka' => ['Matematika', 'Fisika', 'Seni Budaya', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Matematika', 'Fisika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Matematika', 'Fisika', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Matematika', 'Fisika', 'Bahasa Indonesia']
            ],
            'Teknik Sipil' => [
                'required' => ['Matematika', 'Fisika'],
                'preferred' => ['Matematika', 'Fisika', 'Kimia'],
                'kurikulum_merdeka' => ['Matematika', 'Fisika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Matematika', 'Fisika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Matematika', 'Fisika', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Matematika', 'Fisika', 'Bahasa Indonesia']
            ],
            'Teknik Kimia' => [
                'required' => ['Matematika', 'Kimia'],
                'preferred' => ['Matematika', 'Kimia', 'Fisika'],
                'kurikulum_merdeka' => ['Matematika', 'Kimia', 'Fisika', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Matematika', 'Kimia', 'Fisika', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Matematika', 'Kimia', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Matematika', 'Kimia', 'Bahasa Indonesia']
            ],
            'Teknik Industri' => [
                'required' => ['Matematika', 'Fisika'],
                'preferred' => ['Matematika', 'Fisika', 'Ekonomi'],
                'kurikulum_merdeka' => ['Matematika', 'Fisika', 'Ekonomi', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Matematika', 'Fisika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Matematika', 'Fisika', 'Ekonomi', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Matematika', 'Fisika', 'Bahasa Indonesia']
            ],

            // SAINTEK - Kedokteran & Kesehatan
            'Ilmu Kedokteran' => [
                'required' => ['Biologi', 'Kimia'],
                'preferred' => ['Biologi', 'Kimia', 'Fisika', 'Matematika'],
                'kurikulum_merdeka' => ['Biologi', 'Kimia', 'Fisika', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Biologi', 'Kimia', 'Fisika', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Biologi', 'Kimia', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Biologi', 'Kimia', 'Bahasa Indonesia']
            ],
            'Ilmu Kedokteran Gigi' => [
                'required' => ['Biologi', 'Kimia'],
                'preferred' => ['Biologi', 'Kimia', 'Fisika', 'Matematika'],
                'kurikulum_merdeka' => ['Biologi', 'Kimia', 'Fisika', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Biologi', 'Kimia', 'Fisika', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Biologi', 'Kimia', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Biologi', 'Kimia', 'Bahasa Indonesia']
            ],
            'Ilmu Farmasi' => [
                'required' => ['Kimia', 'Biologi'],
                'preferred' => ['Kimia', 'Biologi', 'Matematika'],
                'kurikulum_merdeka' => ['Kimia', 'Biologi', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Kimia', 'Biologi', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Kimia', 'Biologi', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Kimia', 'Biologi', 'Bahasa Indonesia']
            ],
            'Ilmu Gizi' => [
                'required' => ['Biologi', 'Kimia'],
                'preferred' => ['Biologi', 'Kimia', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Biologi', 'Kimia', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Biologi', 'Kimia', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Biologi', 'Kimia', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Biologi', 'Kimia', 'Bahasa Indonesia']
            ],
            'Keperawatan' => [
                'required' => ['Biologi', 'Kimia'],
                'preferred' => ['Biologi', 'Kimia', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Biologi', 'Kimia', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Biologi', 'Kimia', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Biologi', 'Kimia', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Biologi', 'Kimia', 'Bahasa Indonesia']
            ],

            // SAINTEK - Sains Murni
            'Matematika' => [
                'required' => ['Matematika'],
                'preferred' => ['Matematika', 'Fisika'],
                'kurikulum_merdeka' => ['Matematika', 'Fisika', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Matematika', 'Fisika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Matematika', 'Bahasa Indonesia']
            ],
            'Fisika' => [
                'required' => ['Fisika', 'Matematika'],
                'preferred' => ['Fisika', 'Matematika', 'Kimia'],
                'kurikulum_merdeka' => ['Fisika', 'Matematika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Fisika', 'Matematika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Fisika', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Fisika', 'Matematika', 'Bahasa Indonesia']
            ],
            'Kimia' => [
                'required' => ['Kimia', 'Matematika'],
                'preferred' => ['Kimia', 'Matematika', 'Fisika'],
                'kurikulum_merdeka' => ['Kimia', 'Matematika', 'Fisika', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Kimia', 'Matematika', 'Fisika', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Kimia', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Kimia', 'Matematika', 'Bahasa Indonesia']
            ],
            'Biologi' => [
                'required' => ['Biologi', 'Kimia'],
                'preferred' => ['Biologi', 'Kimia', 'Matematika'],
                'kurikulum_merdeka' => ['Biologi', 'Kimia', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Biologi', 'Kimia', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Biologi', 'Kimia', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Biologi', 'Kimia', 'Bahasa Indonesia']
            ],
            'Bioteknologi' => [
                'required' => ['Biologi', 'Kimia'],
                'preferred' => ['Biologi', 'Kimia', 'Matematika'],
                'kurikulum_merdeka' => ['Biologi', 'Kimia', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Biologi', 'Kimia', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Biologi', 'Kimia', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Biologi', 'Kimia', 'Bahasa Indonesia']
            ],
            'Biofisika' => [
                'required' => ['Biologi', 'Fisika'],
                'preferred' => ['Biologi', 'Fisika', 'Matematika'],
                'kurikulum_merdeka' => ['Biologi', 'Fisika', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Biologi', 'Fisika', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Biologi', 'Fisika', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Biologi', 'Fisika', 'Bahasa Indonesia']
            ],
            'Astronomi' => [
                'required' => ['Fisika', 'Matematika'],
                'preferred' => ['Fisika', 'Matematika', 'Kimia'],
                'kurikulum_merdeka' => ['Fisika', 'Matematika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Fisika', 'Matematika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Fisika', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Fisika', 'Matematika', 'Bahasa Indonesia']
            ],
            'Ilmu Kebumian' => [
                'required' => ['Fisika', 'Kimia'],
                'preferred' => ['Fisika', 'Kimia', 'Matematika'],
                'kurikulum_merdeka' => ['Fisika', 'Kimia', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Fisika', 'Kimia', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Fisika', 'Kimia', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Fisika', 'Kimia', 'Bahasa Indonesia']
            ],
            'Ilmu Kelautan' => [
                'required' => ['Biologi', 'Fisika'],
                'preferred' => ['Biologi', 'Fisika', 'Kimia'],
                'kurikulum_merdeka' => ['Biologi', 'Fisika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Biologi', 'Fisika', 'Kimia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Biologi', 'Fisika', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Biologi', 'Fisika', 'Bahasa Indonesia']
            ],

            // SOSHUM - Ekonomi & Bisnis
            'Ekonomi' => [
                'required' => ['Matematika', 'Ekonomi'],
                'preferred' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Manajemen' => [
                'required' => ['Matematika', 'Ekonomi'],
                'preferred' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Akuntansi' => [
                'required' => ['Matematika', 'Ekonomi'],
                'preferred' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Administrasi Bisnis' => [
                'required' => ['Matematika', 'Ekonomi'],
                'preferred' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Bisnis' => [
                'required' => ['Matematika', 'Ekonomi'],
                'preferred' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Matematika', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris']
            ],

            // SOSHUM - Hukum & Politik
            'Hukum' => [
                'required' => ['Bahasa Indonesia', 'PPKn'],
                'preferred' => ['Bahasa Indonesia', 'PPKn', 'Sejarah Indonesia'],
                'kurikulum_merdeka' => ['Bahasa Indonesia', 'PPKn', 'Sejarah Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Bahasa Indonesia', 'PPKn', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Bahasa Indonesia', 'PPKn', 'Sejarah Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Bahasa Indonesia', 'PPKn', 'Bahasa Inggris']
            ],
            'Ilmu Politik' => [
                'required' => ['PPKn', 'Sejarah Indonesia'],
                'preferred' => ['PPKn', 'Sejarah Indonesia', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['PPKn', 'Sejarah Indonesia', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['PPKn', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['PPKn', 'Sejarah Indonesia', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['PPKn', 'Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Hubungan Internasional' => [
                'required' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'preferred' => ['Bahasa Indonesia', 'Bahasa Inggris', 'PPKn'],
                'kurikulum_merdeka' => ['Bahasa Indonesia', 'Bahasa Inggris', 'PPKn'],
                'kurikulum_2013_ipa' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Bahasa Indonesia', 'Bahasa Inggris', 'PPKn'],
                'kurikulum_2013_bahasa' => ['Bahasa Indonesia', 'Bahasa Inggris']
            ],

            // SOSHUM - Sosial & Komunikasi
            'Sosiologi' => [
                'required' => ['Sosiologi', 'Bahasa Indonesia'],
                'preferred' => ['Sosiologi', 'Bahasa Indonesia', 'Sejarah Indonesia'],
                'kurikulum_merdeka' => ['Sosiologi', 'Bahasa Indonesia', 'Sejarah Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Sosiologi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Sosiologi', 'Bahasa Indonesia', 'Sejarah Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Sosiologi', 'Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Antropologi' => [
                'required' => ['Sosiologi', 'Bahasa Indonesia'],
                'preferred' => ['Sosiologi', 'Bahasa Indonesia', 'Sejarah Indonesia'],
                'kurikulum_merdeka' => ['Sosiologi', 'Bahasa Indonesia', 'Sejarah Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Sosiologi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Sosiologi', 'Bahasa Indonesia', 'Sejarah Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Sosiologi', 'Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Psikologi' => [
                'required' => ['Biologi', 'Bahasa Indonesia'],
                'preferred' => ['Biologi', 'Bahasa Indonesia', 'Matematika'],
                'kurikulum_merdeka' => ['Biologi', 'Bahasa Indonesia', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Biologi', 'Bahasa Indonesia', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Biologi', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Biologi', 'Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Ilmu Komunikasi' => [
                'required' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'preferred' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Sosiologi'],
                'kurikulum_merdeka' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Sosiologi'],
                'kurikulum_2013_ipa' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Sosiologi'],
                'kurikulum_2013_bahasa' => ['Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Jurnalistik' => [
                'required' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'preferred' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Sosiologi'],
                'kurikulum_merdeka' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Sosiologi'],
                'kurikulum_2013_ipa' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Sosiologi'],
                'kurikulum_2013_bahasa' => ['Bahasa Indonesia', 'Bahasa Inggris']
            ],

            // SOSHUM - Bahasa & Sastra
            'Sastra Indonesia' => [
                'required' => ['Bahasa Indonesia'],
                'preferred' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_merdeka' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Sastra Inggris' => [
                'required' => ['Bahasa Inggris'],
                'preferred' => ['Bahasa Inggris', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Bahasa Inggris', 'Bahasa Indonesia'],
                'kurikulum_2013_ipa' => ['Bahasa Inggris', 'Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['Bahasa Inggris', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Bahasa Inggris', 'Bahasa Indonesia']
            ],
            'Linguistik' => [
                'required' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'preferred' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_merdeka' => ['Bahasa Indonesia Tingkat Lanjut', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Bahasa Indonesia', 'Bahasa Inggris']
            ],

            // SOSHUM - Sejarah & Budaya
            'Ilmu Sejarah' => [
                'required' => ['Sejarah Indonesia'],
                'preferred' => ['Sejarah Indonesia', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Sejarah'],
                'kurikulum_2013_ipa' => ['Sejarah Indonesia', 'Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['Sejarah Indonesia', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Sejarah Indonesia', 'Bahasa Indonesia']
            ],
            'Arkeologi' => [
                'required' => ['Sejarah Indonesia'],
                'preferred' => ['Sejarah Indonesia', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Sejarah'],
                'kurikulum_2013_ipa' => ['Sejarah Indonesia', 'Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['Sejarah Indonesia', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Sejarah Indonesia', 'Bahasa Indonesia']
            ],
            'Filsafat' => [
                'required' => ['Bahasa Indonesia'],
                'preferred' => ['Bahasa Indonesia', 'PPKn'],
                'kurikulum_merdeka' => ['Bahasa Indonesia', 'PPKn'],
                'kurikulum_2013_ipa' => ['Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['Bahasa Indonesia', 'PPKn'],
                'kurikulum_2013_bahasa' => ['Bahasa Indonesia']
            ],

            // SOSHUM - Geografi & Pariwisata
            'Geografi' => [
                'required' => ['Geografi'],
                'preferred' => ['Geografi', 'Matematika'],
                'kurikulum_merdeka' => ['Geografi', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Geografi', 'Matematika', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Geografi', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Geografi', 'Matematika', 'Bahasa Indonesia']
            ],
            'Pariwisata' => [
                'required' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'preferred' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Geografi'],
                'kurikulum_merdeka' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Geografi'],
                'kurikulum_2013_ipa' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Geografi'],
                'kurikulum_2013_bahasa' => ['Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Perhotelan' => [
                'required' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'preferred' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Ekonomi'],
                'kurikulum_merdeka' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Ekonomi'],
                'kurikulum_2013_ipa' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Ekonomi'],
                'kurikulum_2013_bahasa' => ['Bahasa Indonesia', 'Bahasa Inggris']
            ],

            // SENI & DESAIN
            'Seni' => [
                'required' => ['Seni Budaya'],
                'preferred' => ['Seni Budaya', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Seni Budaya', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Seni Budaya', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Seni Budaya', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Seni Budaya', 'Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Desain' => [
                'required' => ['Seni Budaya', 'Matematika'],
                'preferred' => ['Seni Budaya', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Seni Budaya', 'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Seni Budaya', 'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Seni Budaya', 'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Seni Budaya', 'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris']
            ],

            // PENDIDIKAN
            'Pendidikan Matematika' => [
                'required' => ['Matematika'],
                'preferred' => ['Matematika', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Pendidikan Bahasa Indonesia' => [
                'required' => ['Bahasa Indonesia'],
                'preferred' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_merdeka' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ipa' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_ips' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa' => ['Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Pendidikan Bahasa Inggris' => [
                'required' => ['Bahasa Inggris'],
                'preferred' => ['Bahasa Inggris', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Bahasa Inggris', 'Bahasa Indonesia'],
                'kurikulum_2013_ipa' => ['Bahasa Inggris', 'Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['Bahasa Inggris', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Bahasa Inggris', 'Bahasa Indonesia']
            ],
            'Pendidikan Fisika' => [
                'required' => ['Fisika'],
                'preferred' => ['Fisika', 'Matematika'],
                'kurikulum_merdeka' => ['Fisika', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_ipa' => ['Fisika', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['Fisika', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Fisika', 'Matematika', 'Bahasa Indonesia']
            ],
            'Pendidikan Kimia' => [
                'required' => ['Kimia'],
                'preferred' => ['Kimia', 'Matematika'],
                'kurikulum_merdeka' => ['Kimia', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_ipa' => ['Kimia', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['Kimia', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Kimia', 'Matematika', 'Bahasa Indonesia']
            ],
            'Pendidikan Biologi' => [
                'required' => ['Biologi'],
                'preferred' => ['Biologi', 'Kimia'],
                'kurikulum_merdeka' => ['Biologi', 'Kimia', 'Bahasa Indonesia'],
                'kurikulum_2013_ipa' => ['Biologi', 'Kimia', 'Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['Biologi', 'Kimia', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Biologi', 'Kimia', 'Bahasa Indonesia']
            ],
            'Pendidikan Sejarah' => [
                'required' => ['Sejarah Indonesia'],
                'preferred' => ['Sejarah Indonesia', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Sejarah'],
                'kurikulum_2013_ipa' => ['Sejarah Indonesia', 'Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['Sejarah Indonesia', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Sejarah Indonesia', 'Bahasa Indonesia']
            ],
            'Pendidikan Geografi' => [
                'required' => ['Geografi'],
                'preferred' => ['Geografi', 'Matematika'],
                'kurikulum_merdeka' => ['Geografi', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_ipa' => ['Geografi', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['Geografi', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Geografi', 'Matematika', 'Bahasa Indonesia']
            ],
            'Pendidikan Ekonomi' => [
                'required' => ['Ekonomi'],
                'preferred' => ['Ekonomi', 'Matematika'],
                'kurikulum_merdeka' => ['Ekonomi', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_ipa' => ['Ekonomi', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['Ekonomi', 'Matematika', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Ekonomi', 'Matematika', 'Bahasa Indonesia']
            ],
            'Pendidikan Sosiologi' => [
                'required' => ['Sosiologi'],
                'preferred' => ['Sosiologi', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Sosiologi', 'Bahasa Indonesia'],
                'kurikulum_2013_ipa' => ['Sosiologi', 'Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['Sosiologi', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Sosiologi', 'Bahasa Indonesia']
            ],
            'Pendidikan Seni' => [
                'required' => ['Seni Budaya'],
                'preferred' => ['Seni Budaya', 'Bahasa Indonesia'],
                'kurikulum_merdeka' => ['Seni Budaya', 'Bahasa Indonesia'],
                'kurikulum_2013_ipa' => ['Seni Budaya', 'Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['Seni Budaya', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['Seni Budaya', 'Bahasa Indonesia']
            ],
            'Pendidikan Jasmani' => [
                'required' => ['PJOK'],
                'preferred' => ['PJOK', 'Biologi'],
                'kurikulum_merdeka' => ['PJOK', 'Biologi', 'Bahasa Indonesia'],
                'kurikulum_2013_ipa' => ['PJOK', 'Biologi', 'Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['PJOK', 'Biologi', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['PJOK', 'Biologi', 'Bahasa Indonesia']
            ],
            'Bimbingan Konseling' => [
                'required' => ['Bahasa Indonesia'],
                'preferred' => ['Bahasa Indonesia', 'Psikologi'],
                'kurikulum_merdeka' => ['Bahasa Indonesia', 'Psikologi'],
                'kurikulum_2013_ipa' => ['Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['Bahasa Indonesia', 'Psikologi'],
                'kurikulum_2013_bahasa' => ['Bahasa Indonesia']
            ],

            // OLAHRAGA & KESEHATAN
            'Ilmu Keolahragaan' => [
                'required' => ['PJOK', 'Biologi'],
                'preferred' => ['PJOK', 'Biologi'],
                'kurikulum_merdeka' => ['PJOK', 'Biologi', 'Bahasa Indonesia'],
                'kurikulum_2013_ipa' => ['PJOK', 'Biologi', 'Bahasa Indonesia'],
                'kurikulum_2013_ips' => ['PJOK', 'Biologi', 'Bahasa Indonesia'],
                'kurikulum_2013_bahasa' => ['PJOK', 'Biologi', 'Bahasa Indonesia']
            ]
        ];

        foreach ($allMajors as $majorName => $subjects) {
            $this->updateMajor($majorName, $subjects);
        }
    }

    private function updateMajor($majorName, $subjects)
    {
        $major = MajorRecommendation::where('major_name', $majorName)->first();
        
        if ($major) {
            $major->update([
                'required_subjects' => $subjects['required'],
                'preferred_subjects' => $subjects['preferred'],
                'kurikulum_merdeka_subjects' => $subjects['kurikulum_merdeka'],
                'kurikulum_2013_ipa_subjects' => $subjects['kurikulum_2013_ipa'],
                'kurikulum_2013_ips_subjects' => $subjects['kurikulum_2013_ips'],
                'kurikulum_2013_bahasa_subjects' => $subjects['kurikulum_2013_bahasa']
            ]);
            
            $this->command->info("✅ Updated: {$majorName}");
        } else {
            $this->command->warn("❌ Major not found: {$majorName}");
        }
    }
}
