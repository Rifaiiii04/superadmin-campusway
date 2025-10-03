<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddOptionalSubjectsCommand extends Command
{
    protected $signature = 'subjects:add-optional';
    protected $description = 'Add optional subjects for SMA and SMK';

    public function handle()
    {
        $this->info('📚 Menambahkan mata pelajaran pilihan...');

        // Mata pelajaran pilihan untuk SMA/MA
        $smaOptionalSubjects = [
            [
                'name' => 'Fisika',
                'code' => 'FIS',
                'description' => 'Mata pelajaran pilihan - Fisika',
                'education_level' => 'SMA/MA',
                'subject_type' => 'Pilihan',
                'subject_number' => 4,
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Kimia',
                'code' => 'KIM',
                'description' => 'Mata pelajaran pilihan - Kimia',
                'education_level' => 'SMA/MA',
                'subject_type' => 'Pilihan',
                'subject_number' => 5,
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Biologi',
                'code' => 'BIO',
                'description' => 'Mata pelajaran pilihan - Biologi',
                'education_level' => 'SMA/MA',
                'subject_type' => 'Pilihan',
                'subject_number' => 6,
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Ekonomi',
                'code' => 'EKO',
                'description' => 'Mata pelajaran pilihan - Ekonomi',
                'education_level' => 'SMA/MA',
                'subject_type' => 'Pilihan',
                'subject_number' => 7,
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Sejarah',
                'code' => 'SEJ',
                'description' => 'Mata pelajaran pilihan - Sejarah',
                'education_level' => 'SMA/MA',
                'subject_type' => 'Pilihan',
                'subject_number' => 8,
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Geografi',
                'code' => 'GEO',
                'description' => 'Mata pelajaran pilihan - Geografi',
                'education_level' => 'SMA/MA',
                'subject_type' => 'Pilihan',
                'subject_number' => 9,
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Mata pelajaran pilihan untuk SMK/MAK
        $smkOptionalSubjects = [
            [
                'name' => 'Teknik Komputer dan Jaringan',
                'code' => 'TKJ',
                'description' => 'Mata pelajaran pilihan SMK - Teknik Komputer dan Jaringan',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 4,
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Teknik Kendaraan Ringan',
                'code' => 'TKR',
                'description' => 'Mata pelajaran pilihan SMK - Teknik Kendaraan Ringan',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 5,
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Teknik Mesin',
                'code' => 'TM',
                'description' => 'Mata pelajaran pilihan SMK - Teknik Mesin',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 6,
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Akuntansi',
                'code' => 'AK',
                'description' => 'Mata pelajaran pilihan SMK - Akuntansi',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 7,
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Administrasi Perkantoran',
                'code' => 'AP',
                'description' => 'Mata pelajaran pilihan SMK - Administrasi Perkantoran',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Pilihan',
                'subject_number' => 8,
                'is_required' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Insert mata pelajaran pilihan SMA/MA
        foreach ($smaOptionalSubjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                [
                    'name' => $subject['name'],
                    'education_level' => $subject['education_level']
                ],
                $subject
            );
        }

        // Insert mata pelajaran pilihan SMK/MAK
        foreach ($smkOptionalSubjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                [
                    'name' => $subject['name'],
                    'education_level' => $subject['education_level']
                ],
                $subject
            );
        }

        // Pastikan Produk/PKK ada untuk SMK
        $produkPKK = [
            'name' => 'Produk/Projek Kreatif dan Kewirausahaan',
            'code' => 'PPKK',
            'description' => 'Mata pelajaran pilihan wajib untuk SMK/MAK - Produk/Projek Kreatif dan Kewirausahaan',
            'education_level' => 'SMK/MAK',
            'subject_type' => 'Pilihan_Wajib',
            'subject_number' => 9,
            'is_required' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('subjects')->updateOrInsert(
            [
                'name' => $produkPKK['name'],
                'education_level' => $produkPKK['education_level']
            ],
            $produkPKK
        );

        $this->info('✅ Mata pelajaran pilihan SMA/MA: ' . count($smaOptionalSubjects) . ' mata pelajaran');
        $this->info('✅ Mata pelajaran pilihan SMK/MAK: ' . count($smkOptionalSubjects) . ' mata pelajaran');
        $this->info('✅ Produk/PKK untuk SMK/MAK: Tersedia');

        // Cek hasil
        $smaCount = DB::table('subjects')
            ->where('education_level', 'SMA/MA')
            ->where('subject_type', 'Pilihan')
            ->count();

        $smkCount = DB::table('subjects')
            ->where('education_level', 'SMK/MAK')
            ->where('subject_type', 'Pilihan')
            ->count();

        $this->info("📊 Total mata pelajaran pilihan SMA/MA: {$smaCount}");
        $this->info("📊 Total mata pelajaran pilihan SMK/MAK: {$smkCount}");

        $this->info('🎉 Penambahan mata pelajaran pilihan selesai!');
    }
}
