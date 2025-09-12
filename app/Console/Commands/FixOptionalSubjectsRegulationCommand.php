<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Subject;

class FixOptionalSubjectsRegulationCommand extends Command
{
    protected $signature = 'subjects:fix-optional-regulation';
    protected $description = 'Fix optional subjects according to regulation (19 subjects for SMA, PKK + 18 for SMK)';

    public function handle()
    {
        $this->info('🔧 Memperbaiki mata pelajaran pilihan sesuai regulasi...');

        try {
            // 1. Hapus semua mata pelajaran pilihan yang ada
            $this->info('🗑️ Menghapus mata pelajaran pilihan lama...');
            Subject::where('subject_type', 'pilihan')->delete();
            Subject::where('subject_type', 'pilihan_wajib')->delete();

            // 2. Buat 19 mata pelajaran pilihan untuk SMA/MA (1-18)
            $this->info('✅ Membuat 18 mata pelajaran pilihan untuk SMA/MA...');
            
            $smaOptionalSubjects = [
                ['name' => 'Matematika Lanjutan', 'code' => 'MAT_LANJUT', 'subject_number' => 1],
                ['name' => 'Bahasa Indonesia Lanjutan', 'code' => 'BIN_LANJUT', 'subject_number' => 2],
                ['name' => 'Bahasa Inggris Lanjutan', 'code' => 'BING_LANJUT', 'subject_number' => 3],
                ['name' => 'Fisika', 'code' => 'FIS', 'subject_number' => 4],
                ['name' => 'Kimia', 'code' => 'KIM', 'subject_number' => 5],
                ['name' => 'Biologi', 'code' => 'BIO', 'subject_number' => 6],
                ['name' => 'Ekonomi', 'code' => 'EKO', 'subject_number' => 7],
                ['name' => 'Sosiologi', 'code' => 'SOS', 'subject_number' => 8],
                ['name' => 'Geografi', 'code' => 'GEO', 'subject_number' => 9],
                ['name' => 'Sejarah', 'code' => 'SEJ', 'subject_number' => 10],
                ['name' => 'Antropologi', 'code' => 'ANT', 'subject_number' => 11],
                ['name' => 'PPKn/Pendidikan Pancasila', 'code' => 'PPKN', 'subject_number' => 12],
                ['name' => 'Bahasa Arab', 'code' => 'BAR', 'subject_number' => 13],
                ['name' => 'Bahasa Jerman', 'code' => 'BJE', 'subject_number' => 14],
                ['name' => 'Bahasa Prancis', 'code' => 'BPR', 'subject_number' => 15],
                ['name' => 'Bahasa Jepang', 'code' => 'BJP', 'subject_number' => 16],
                ['name' => 'Bahasa Korea', 'code' => 'BKO', 'subject_number' => 17],
                ['name' => 'Bahasa Mandarin', 'code' => 'BMA', 'subject_number' => 18],
            ];

            foreach ($smaOptionalSubjects as $subjectData) {
                Subject::create([
                    'name' => $subjectData['name'],
                    'code' => $subjectData['code'],
                    'subject_type' => 'pilihan',
                    'subject_number' => $subjectData['subject_number'],
                    'education_level' => 'SMA/MA'
                ]);
                $this->line("✅ {$subjectData['name']} (SMA/MA)");
            }

            // 3. Buat 19 mata pelajaran pilihan untuk SMK/MAK (1-18 + PKK)
            $this->info('✅ Membuat 19 mata pelajaran pilihan untuk SMK/MAK...');
            
            // 18 mata pelajaran pilihan (1-18)
            foreach ($smaOptionalSubjects as $subjectData) {
                Subject::create([
                    'name' => $subjectData['name'],
                    'code' => $subjectData['code'],
                    'subject_type' => 'pilihan',
                    'subject_number' => $subjectData['subject_number'],
                    'education_level' => 'SMK/MAK'
                ]);
                $this->line("✅ {$subjectData['name']} (SMK/MAK)");
            }

            // Produk/Projek Kreatif dan Kewirausahaan (19) untuk SMK
            Subject::create([
                'name' => 'Produk/Projek Kreatif dan Kewirausahaan',
                'code' => 'PKK',
                'subject_type' => 'pilihan_wajib',
                'subject_number' => 19,
                'education_level' => 'SMK/MAK'
            ]);
            $this->line("✅ Produk/Projek Kreatif dan Kewirausahaan (SMK/MAK) - Pilihan Wajib");

            // 4. Verifikasi hasil
            $this->info('📊 Verifikasi hasil...');
            
            $smaOptional = Subject::where('education_level', 'SMA/MA')
                ->where('subject_type', 'pilihan')
                ->count();
            
            $smkOptional = Subject::where('education_level', 'SMK/MAK')
                ->where('subject_type', 'pilihan')
                ->count();
            
            $smkPKK = Subject::where('education_level', 'SMK/MAK')
                ->where('subject_type', 'pilihan_wajib')
                ->count();

            $this->info("📈 Hasil:");
            $this->line("   - SMA/MA Pilihan: {$smaOptional} (seharusnya 18)");
            $this->line("   - SMK/MAK Pilihan: {$smkOptional} (seharusnya 18)");
            $this->line("   - SMK/MAK PKK: {$smkPKK} (seharusnya 1)");

            if ($smaOptional == 18 && $smkOptional == 18 && $smkPKK == 1) {
                $this->info('🎉 Mata pelajaran pilihan berhasil diperbaiki sesuai regulasi!');
            } else {
                $this->error('❌ Ada masalah dengan mata pelajaran pilihan');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}
