<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSubjectsConstraintCommand extends Command
{
    protected $signature = 'subjects:fix-constraint';
    protected $description = 'Fix subjects unique constraint and data';

    public function handle()
    {
        $this->info('📚 Memperbaiki constraint dan data mata pelajaran...');

        try {
            // Hapus constraint yang bermasalah
            $this->info('🔄 Menghapus constraint subjects_name_unique...');
            DB::statement('ALTER TABLE subjects DROP CONSTRAINT subjects_name_unique');
            $this->info('✅ Constraint dihapus');
        } catch (\Exception $e) {
            $this->warn('⚠️ Constraint tidak ditemukan atau sudah dihapus: ' . $e->getMessage());
        }

        // Hapus semua data subjects yang ada
        $this->info('🔄 Menghapus data subjects lama...');
        
        // Hapus foreign key constraints dulu
        try {
            DB::statement('ALTER TABLE program_studi_subjects DROP CONSTRAINT IF EXISTS program_studi_subjects_subject_id_foreign');
            DB::statement('ALTER TABLE major_subject_mappings DROP CONSTRAINT IF EXISTS major_subject_mappings_subject_id_foreign');
        } catch (\Exception $e) {
            $this->warn('⚠️ Foreign key constraints tidak ditemukan: ' . $e->getMessage());
        }
        
        // Hapus data dari tabel yang mereferensi subjects
        DB::table('program_studi_subjects')->delete();
        DB::table('major_subject_mappings')->delete();
        
        // Hapus data subjects
        DB::table('subjects')->delete();
        $this->info('✅ Data lama dihapus');

        // Insert data baru yang benar
        $this->info('🔄 Menambahkan data subjects baru...');

        // Mata pelajaran wajib SMA/MA
        $smaMandatorySubjects = [
            [
                'name' => 'Bahasa Indonesia',
                'code' => 'BI',
                'description' => 'Mata pelajaran wajib - Bahasa Indonesia',
                'education_level' => 'SMA/MA',
                'subject_type' => 'Wajib',
                'subject_number' => 1,
                'is_required' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Bahasa Inggris',
                'code' => 'BE',
                'description' => 'Mata pelajaran wajib - Bahasa Inggris',
                'education_level' => 'SMA/MA',
                'subject_type' => 'Wajib',
                'subject_number' => 2,
                'is_required' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Matematika',
                'code' => 'MTK',
                'description' => 'Mata pelajaran wajib - Matematika',
                'education_level' => 'SMA/MA',
                'subject_type' => 'Wajib',
                'subject_number' => 3,
                'is_required' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Mata pelajaran wajib SMK/MAK
        $smkMandatorySubjects = [
            [
                'name' => 'Bahasa Indonesia',
                'code' => 'BI',
                'description' => 'Mata pelajaran wajib - Bahasa Indonesia',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Wajib',
                'subject_number' => 1,
                'is_required' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Bahasa Inggris',
                'code' => 'BE',
                'description' => 'Mata pelajaran wajib - Bahasa Inggris',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Wajib',
                'subject_number' => 2,
                'is_required' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Matematika',
                'code' => 'MTK',
                'description' => 'Mata pelajaran wajib - Matematika',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Wajib',
                'subject_number' => 3,
                'is_required' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Mata pelajaran pilihan SMA/MA
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

        // Mata pelajaran pilihan SMK/MAK
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

        // Produk/PKK untuk SMK
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

        // Insert semua data
        $allSubjects = array_merge(
            $smaMandatorySubjects,
            $smkMandatorySubjects,
            $smaOptionalSubjects,
            $smkOptionalSubjects,
            [$produkPKK]
        );

        foreach ($allSubjects as $subject) {
            DB::table('subjects')->insert($subject);
        }

        $this->info('✅ Data subjects baru ditambahkan');

        // Cek hasil
        $smaCount = DB::table('subjects')
            ->where('education_level', 'SMA/MA')
            ->count();

        $smkCount = DB::table('subjects')
            ->where('education_level', 'SMK/MAK')
            ->count();

        $mandatoryCount = DB::table('subjects')
            ->where('subject_type', 'Wajib')
            ->count();

        $optionalCount = DB::table('subjects')
            ->where('subject_type', 'Pilihan')
            ->count();

        $this->info("📊 Total mata pelajaran SMA/MA: {$smaCount}");
        $this->info("📊 Total mata pelajaran SMK/MAK: {$smkCount}");
        $this->info("📊 Total mata pelajaran wajib: {$mandatoryCount}");
        $this->info("📊 Total mata pelajaran pilihan: {$optionalCount}");

        $this->info('🎉 Perbaikan constraint dan data selesai!');
    }
}
