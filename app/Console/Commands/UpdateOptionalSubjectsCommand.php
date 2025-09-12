<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateOptionalSubjectsCommand extends Command
{
    protected $signature = 'subjects:update-optional';
    protected $description = 'Update existing subjects to be optional';

    public function handle()
    {
        $this->info('📚 Memperbarui mata pelajaran menjadi pilihan...');

        // Update mata pelajaran yang sudah ada menjadi pilihan
        $optionalSubjects = [
            'Fisika', 'Kimia', 'Biologi', 'Ekonomi', 'Sejarah', 'Geografi',
            'Teknik Komputer dan Jaringan', 'Teknik Kendaraan Ringan', 'Teknik Mesin',
            'Akuntansi', 'Administrasi Perkantoran'
        ];

        $updatedCount = 0;

        foreach ($optionalSubjects as $subjectName) {
            // Update untuk SMA/MA
            $updated = DB::table('subjects')
                ->where('name', $subjectName)
                ->where('education_level', 'SMA/MA')
                ->update([
                    'subject_type' => 'Pilihan',
                    'is_required' => false,
                    'updated_at' => now()
                ]);

            if ($updated) {
                $updatedCount++;
                $this->line("✅ Updated {$subjectName} (SMA/MA) to Pilihan");
            }

            // Update untuk SMK/MAK
            $updated = DB::table('subjects')
                ->where('name', $subjectName)
                ->where('education_level', 'SMK/MAK')
                ->update([
                    'subject_type' => 'Pilihan',
                    'is_required' => false,
                    'updated_at' => now()
                ]);

            if ($updated) {
                $updatedCount++;
                $this->line("✅ Updated {$subjectName} (SMK/MAK) to Pilihan");
            }
        }

        // Update Produk/PKK untuk SMK jika sudah ada
        $updated = DB::table('subjects')
            ->where('name', 'Produk/Projek Kreatif dan Kewirausahaan')
            ->where('education_level', 'SMK/MAK')
            ->update([
                'subject_type' => 'Pilihan_Wajib',
                'is_required' => false,
                'updated_at' => now()
            ]);

        if ($updated) {
            $this->line("✅ Updated Produk/PKK (SMK/MAK) to Pilihan_Wajib");
        }

        $this->info("✅ Total updated: {$updatedCount} subjects");
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

        $mandatoryCount = DB::table('subjects')
            ->where('subject_type', 'Wajib')
            ->count();

        $this->info("📊 Total mata pelajaran wajib: {$mandatoryCount}");
        $this->info("📊 Total mata pelajaran pilihan SMA/MA: {$smaCount}");
        $this->info("📊 Total mata pelajaran pilihan SMK/MAK: {$smkCount}");

        $this->info('🎉 Update mata pelajaran selesai!');
    }
}
