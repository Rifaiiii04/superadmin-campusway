<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Subject;

class FixMandatorySubjectsFinalCommand extends Command
{
    protected $signature = 'subjects:fix-mandatory-final';
    protected $description = 'Fix mandatory subjects to only 3: Bahasa Indonesia, Matematika, Bahasa Inggris';

    public function handle()
    {
        $this->info('🔧 Memperbaiki mata pelajaran wajib menjadi hanya 3...');

        try {
            // 1. Hapus semua mata pelajaran wajib yang salah
            $this->info('🗑️ Menghapus mata pelajaran wajib yang salah...');
            
            // Hapus mata pelajaran wajib yang bukan 3 yang benar
            $wrongMandatorySubjects = [
                'Teknik Sipil', 'Fisika', 'Kimia', 'Biologi', 'Ekonomi', 
                'Geografi', 'Sejarah', 'Sosiologi', 'Antropologi', 'Psikologi',
                'Desain Komunikasi Visual', 'Produk/Projek Kreatif dan Kewirausahaan'
            ];

            foreach ($wrongMandatorySubjects as $subjectName) {
                $subject = Subject::where('name', $subjectName)
                    ->where('subject_type', 'wajib')
                    ->first();
                
                if ($subject) {
                    $subject->subject_type = 'pilihan';
                    $subject->save();
                    $this->line("✅ {$subjectName} diubah dari wajib ke pilihan");
                }
            }

            // 2. Pastikan 3 mata pelajaran wajib yang benar ada
            $this->info('✅ Memastikan 3 mata pelajaran wajib yang benar...');
            
            $mandatorySubjects = [
                [
                    'name' => 'Bahasa Indonesia',
                    'code' => 'BIN',
                    'subject_type' => 'wajib',
                    'subject_number' => 1,
                    'education_level' => 'SMA/MA'
                ],
                [
                    'name' => 'Bahasa Indonesia',
                    'code' => 'BIN',
                    'subject_type' => 'wajib',
                    'subject_number' => 1,
                    'education_level' => 'SMK/MAK'
                ],
                [
                    'name' => 'Matematika',
                    'code' => 'MAT',
                    'subject_type' => 'wajib',
                    'subject_number' => 2,
                    'education_level' => 'SMA/MA'
                ],
                [
                    'name' => 'Matematika',
                    'code' => 'MAT',
                    'subject_type' => 'wajib',
                    'subject_number' => 2,
                    'education_level' => 'SMK/MAK'
                ],
                [
                    'name' => 'Bahasa Inggris',
                    'code' => 'BING',
                    'subject_type' => 'wajib',
                    'subject_number' => 3,
                    'education_level' => 'SMA/MA'
                ],
                [
                    'name' => 'Bahasa Inggris',
                    'code' => 'BING',
                    'subject_type' => 'wajib',
                    'subject_number' => 3,
                    'education_level' => 'SMK/MAK'
                ]
            ];

            foreach ($mandatorySubjects as $subjectData) {
                Subject::updateOrInsert(
                    [
                        'name' => $subjectData['name'],
                        'education_level' => $subjectData['education_level']
                    ],
                    $subjectData
                );
                $this->line("✅ {$subjectData['name']} ({$subjectData['education_level']}) - Wajib");
            }

            // 3. Pastikan Produk/PKK ada sebagai pilihan khusus untuk SMK
            $this->info('✅ Memastikan Produk/PKK untuk SMK...');
            
            Subject::updateOrInsert(
                [
                    'name' => 'Produk/Projek Kreatif dan Kewirausahaan',
                    'education_level' => 'SMK/MAK'
                ],
                [
                    'name' => 'Produk/Projek Kreatif dan Kewirausahaan',
                    'code' => 'PKK',
                    'subject_type' => 'pilihan_wajib',
                    'subject_number' => 1,
                    'education_level' => 'SMK/MAK'
                ]
            );

            // 4. Verifikasi hasil
            $this->info('📊 Verifikasi hasil...');
            
            $smaMandatory = Subject::where('education_level', 'SMA/MA')
                ->where('subject_type', 'wajib')
                ->count();
            
            $smkMandatory = Subject::where('education_level', 'SMK/MAK')
                ->where('subject_type', 'wajib')
                ->count();
            
            $smkPKK = Subject::where('education_level', 'SMK/MAK')
                ->where('subject_type', 'pilihan_wajib')
                ->count();

            $this->info("📈 Hasil:");
            $this->line("   - SMA/MA Wajib: {$smaMandatory} (seharusnya 3)");
            $this->line("   - SMK/MAK Wajib: {$smkMandatory} (seharusnya 3)");
            $this->line("   - SMK/MAK PKK: {$smkPKK} (seharusnya 1)");

            if ($smaMandatory == 3 && $smkMandatory == 3 && $smkPKK == 1) {
                $this->info('🎉 Mata pelajaran wajib berhasil diperbaiki!');
            } else {
                $this->error('❌ Ada masalah dengan mata pelajaran wajib');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}
