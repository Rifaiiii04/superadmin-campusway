<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSubjectsEducationLevelCommand extends Command
{
    protected $signature = 'subjects:fix-education-level';
    protected $description = 'Fix education level for subjects';

    public function handle()
    {
        $this->info('📚 Memperbaiki education level mata pelajaran...');

        // Update mata pelajaran wajib
        $mandatorySubjects = ['Bahasa Indonesia', 'Bahasa Inggris', 'Matematika'];
        
        foreach ($mandatorySubjects as $subjectName) {
            // Update untuk SMA/MA
            $updated = DB::table('subjects')
                ->where('name', $subjectName)
                ->where('education_level', 'Umum')
                ->update([
                    'education_level' => 'SMA/MA',
                    'subject_type' => 'Wajib',
                    'subject_number' => array_search($subjectName, $mandatorySubjects) + 1,
                    'is_required' => true,
                    'updated_at' => now()
                ]);

            if ($updated) {
                $this->line("✅ Updated {$subjectName} to SMA/MA (Wajib)");
            }

            // Duplicate untuk SMK/MAK
            $subject = DB::table('subjects')
                ->where('name', $subjectName)
                ->where('education_level', 'SMA/MA')
                ->first();

            if ($subject) {
                DB::table('subjects')->updateOrInsert(
                    [
                        'name' => $subject->name,
                        'education_level' => 'SMK/MAK'
                    ],
                    [
                        'code' => $subject->code,
                        'description' => $subject->description,
                        'subject_type' => 'Wajib',
                        'subject_number' => $subject->subject_number,
                        'is_required' => true,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
                $this->line("✅ Created/Updated {$subjectName} for SMK/MAK (Wajib)");
            }
        }

        // Update mata pelajaran pilihan yang sudah ada
        $optionalSubjects = [
            'Fisika', 'Kimia', 'Biologi', 'Ekonomi', 'Sejarah', 'Geografi',
            'Teknik Komputer dan Jaringan', 'Teknik Kendaraan Ringan', 'Teknik Mesin',
            'Akuntansi', 'Administrasi Perkantoran'
        ];

        $subjectNumber = 4;
        foreach ($optionalSubjects as $subjectName) {
            $existing = DB::table('subjects')
                ->where('name', $subjectName)
                ->where('education_level', 'Umum')
                ->first();

            if ($existing) {
                // Update untuk SMA/MA
                DB::table('subjects')
                    ->where('id', $existing->id)
                    ->update([
                        'education_level' => 'SMA/MA',
                        'subject_type' => 'Pilihan',
                        'subject_number' => $subjectNumber,
                        'is_required' => false,
                        'updated_at' => now()
                    ]);

                // Duplicate untuk SMK/MAK
                DB::table('subjects')->updateOrInsert(
                    [
                        'name' => $existing->name,
                        'education_level' => 'SMK/MAK'
                    ],
                    [
                        'code' => $existing->code,
                        'description' => $existing->description,
                        'subject_type' => 'Pilihan',
                        'subject_number' => $subjectNumber,
                        'is_required' => false,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

                $this->line("✅ Updated {$subjectName} to SMA/MA and SMK/MAK (Pilihan)");
                $subjectNumber++;
            }
        }

        // Pastikan Produk/PKK ada untuk SMK
        $produkPKK = [
            'name' => 'Produk/Projek Kreatif dan Kewirausahaan',
            'code' => 'PPKK',
            'description' => 'Mata pelajaran pilihan wajib untuk SMK/MAK - Produk/Projek Kreatif dan Kewirausahaan',
            'education_level' => 'SMK/MAK',
            'subject_type' => 'Pilihan_Wajib',
            'subject_number' => 20,
            'is_required' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ];

        $existing = DB::table('subjects')
            ->where('name', $produkPKK['name'])
            ->where('education_level', 'SMK/MAK')
            ->first();

        DB::table('subjects')->updateOrInsert(
            [
                'name' => $produkPKK['name'],
                'education_level' => $produkPKK['education_level']
            ],
            $produkPKK
        );
        $this->line("✅ Created/Updated Produk/PKK for SMK/MAK");

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

        $this->info("📊 Total mata pelajaran SMA/MA: {$smaCount}");
        $this->info("📊 Total mata pelajaran SMK/MAK: {$smkCount}");
        $this->info("📊 Total mata pelajaran wajib: {$mandatoryCount}");

        $this->info('🎉 Perbaikan education level selesai!');
    }
}
