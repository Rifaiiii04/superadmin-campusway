<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing subjects
        DB::table('subjects')->truncate();

        $subjects = [
            // MATA PELAJARAN WAJIB
            ['name' => 'Bahasa Indonesia', 'code' => 'BIN', 'is_required' => true, 'is_active' => true, 'education_level' => 'Umum', 'subject_type' => 'Wajib', 'subject_number' => 1],
            ['name' => 'Matematika', 'code' => 'MTK', 'is_required' => true, 'is_active' => true, 'education_level' => 'Umum', 'subject_type' => 'Wajib', 'subject_number' => 2],
            ['name' => 'Bahasa Inggris', 'code' => 'BIG', 'is_required' => true, 'is_active' => true, 'education_level' => 'Umum', 'subject_type' => 'Wajib', 'subject_number' => 3],

            // MATA PELAJARAN PILIHAN (1-18)
            ['name' => 'Matematika Lanjutan', 'code' => 'MTK_L', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 1],
            ['name' => 'Bahasa Indonesia Lanjutan', 'code' => 'BIN_L', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 2],
            ['name' => 'Bahasa Inggris Lanjutan', 'code' => 'BIG_L', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 3],
            ['name' => 'Fisika', 'code' => 'FIS', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 4],
            ['name' => 'Kimia', 'code' => 'KIM', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 5],
            ['name' => 'Biologi', 'code' => 'BIO', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 6],
            ['name' => 'Ekonomi', 'code' => 'EKO', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 7],
            ['name' => 'Sosiologi', 'code' => 'SOS', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 8],
            ['name' => 'Geografi', 'code' => 'GEO', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 9],
            ['name' => 'Sejarah', 'code' => 'SEJ', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 10],
            ['name' => 'Antropologi', 'code' => 'ANT', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 11],
            ['name' => 'PPKn / Pendidikan Pancasila', 'code' => 'PPKN', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 12],
            ['name' => 'Bahasa Arab', 'code' => 'BAR', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 13],
            ['name' => 'Bahasa Jerman', 'code' => 'BJE', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 14],
            ['name' => 'Bahasa Prancis', 'code' => 'BPR', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 15],
            ['name' => 'Bahasa Jepang', 'code' => 'BJP', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 16],
            ['name' => 'Bahasa Korea', 'code' => 'BKO', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 17],
            ['name' => 'Bahasa Mandarin', 'code' => 'BMA', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 18],

            // MATA PELAJARAN KHUSUS SMK/MAK
            ['name' => 'Produk/Projek Kreatif dan Kewirausahaan', 'code' => 'PKK', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMK/MAK', 'subject_type' => 'Produk_Kreatif_Kewirausahaan', 'subject_number' => 19],

            // MATA PELAJARAN TAMBAHAN UNTUK KURIKULUM MERDEKA
            ['name' => 'Seni Budaya', 'code' => 'SB', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 20],
            ['name' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'code' => 'PJOK', 'is_required' => false, 'is_active' => true, 'education_level' => 'SMA/MA', 'subject_type' => 'Pilihan', 'subject_number' => 21],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->insert($subject);
        }
    }
}