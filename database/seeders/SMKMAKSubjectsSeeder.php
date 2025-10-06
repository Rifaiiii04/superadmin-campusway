<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SMKMAKSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Berdasarkan ketentuan Pusmendik Kemdikbud:
     * - Pilihan pertama: Mata pelajaran produk/projek kreatif dan kewirausahaan (19)
     * - Pilihan kedua: Mata pelajaran pilihan pada angka (1) sampai dengan angka (18)
     */
    public function run(): void
    {
        // Mata Pelajaran SMK/MAK sesuai ketentuan Pusmendik Kemdikbud
        $smkMakSubjects = [
            // Mata Pelajaran Pilihan SMK/MAK (1-18)
            [
                'name' => 'Teknik Komputer dan Jaringan',
                'code' => 'TKJ',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 1,
                'description' => 'Mata pelajaran yang mempelajari perakitan, instalasi, dan konfigurasi komputer serta jaringan',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Teknik Kendaraan Ringan',
                'code' => 'TKR',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 2,
                'description' => 'Mata pelajaran yang mempelajari perawatan dan perbaikan kendaraan ringan',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Teknik Mesin',
                'code' => 'TM',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 3,
                'description' => 'Mata pelajaran yang mempelajari perancangan, pembuatan, dan perawatan mesin',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Teknik Elektronika',
                'code' => 'TE',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 4,
                'description' => 'Mata pelajaran yang mempelajari komponen elektronika dan sistem elektronik',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Teknik Listrik',
                'code' => 'TL',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 5,
                'description' => 'Mata pelajaran yang mempelajari instalasi dan perawatan sistem kelistrikan',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Teknik Sipil',
                'code' => 'TS',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 6,
                'description' => 'Mata pelajaran yang mempelajari perancangan dan konstruksi bangunan',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Teknik Kimia',
                'code' => 'TK',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 7,
                'description' => 'Mata pelajaran yang mempelajari proses kimia dan pengolahan bahan',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Teknik Pendingin dan Tata Udara',
                'code' => 'TPTU',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 8,
                'description' => 'Mata pelajaran yang mempelajari sistem pendingin dan tata udara',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Teknik Otomotif',
                'code' => 'TO',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 9,
                'description' => 'Mata pelajaran yang mempelajari teknologi otomotif dan perawatan kendaraan',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Teknik Informatika',
                'code' => 'TI',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 10,
                'description' => 'Mata pelajaran yang mempelajari pengembangan perangkat lunak dan sistem informasi',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Akuntansi',
                'code' => 'AK',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 11,
                'description' => 'Mata pelajaran yang mempelajari pencatatan dan pelaporan keuangan',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Administrasi Perkantoran',
                'code' => 'AP',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 12,
                'description' => 'Mata pelajaran yang mempelajari tata kelola administrasi perkantoran',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pemasaran',
                'code' => 'PM',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 13,
                'description' => 'Mata pelajaran yang mempelajari strategi dan teknik pemasaran',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Perbankan',
                'code' => 'PB',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 14,
                'description' => 'Mata pelajaran yang mempelajari operasional perbankan dan keuangan',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Perhotelan',
                'code' => 'PH',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 15,
                'description' => 'Mata pelajaran yang mempelajari manajemen dan operasional perhotelan',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Tata Boga',
                'code' => 'TB',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 16,
                'description' => 'Mata pelajaran yang mempelajari seni kuliner dan tata boga',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Tata Busana',
                'code' => 'TBU',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 17,
                'description' => 'Mata pelajaran yang mempelajari desain dan pembuatan busana',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Desain Komunikasi Visual',
                'code' => 'DKV',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 18,
                'description' => 'Mata pelajaran yang mempelajari desain grafis dan komunikasi visual',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Mata Pelajaran Produk/Projek Kreatif dan Kewirausahaan (19) - Pilihan Pertama
            [
                'name' => 'Produk/Projek Kreatif dan Kewirausahaan',
                'code' => 'PPKK',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Produk_Kreatif_Kewirausahaan',
                'subject_number' => 19,
                'description' => 'Mata pelajaran yang mempelajari pengembangan produk kreatif dan kewirausahaan',
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        // Insert mata pelajaran SMK/MAK
        DB::table('subjects')->insert($smkMakSubjects);

        // Update mata pelajaran yang sudah ada untuk SMA/MA
        DB::table('subjects')->whereIn('name', [
            'Bahasa Indonesia', 'Matematika', 'Bahasa Inggris', 'Fisika', 'Kimia', 
            'Biologi', 'Ekonomi', 'Sejarah', 'Geografi'
        ])->update([
            'education_level' => 'SMA/MA',
            'subject_type' => 'Wajib',
            'updated_at' => now()
        ]);

        // Update mata pelajaran yang sudah ada untuk umum
        DB::table('subjects')->whereNotIn('name', [
            'Bahasa Indonesia', 'Matematika', 'Bahasa Inggris', 'Fisika', 'Kimia', 
            'Biologi', 'Ekonomi', 'Sejarah', 'Geografi'
        ])->where('education_level', 'Umum')->update([
            'education_level' => 'SMA/MA',
            'subject_type' => 'Pilihan',
            'updated_at' => now()
        ]);
    }
}