<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSMASubjectsCommand extends Command
{
    protected $signature = 'subjects:fix-sma';
    protected $description = 'Fix SMA subjects to 3 mandatory + 2 optional';

    public function handle()
    {
        $this->info('📚 Memperbaiki mata pelajaran SMA menjadi 3 wajib + 2 pilihan...');

        // Hapus mata pelajaran SMA yang berlebihan
        $this->info('🔄 Menghapus mata pelajaran SMA yang berlebihan...');
        
        // Hapus mapping lama untuk SMA
        DB::table('major_subject_mappings')
            ->where('education_level', 'SMA/MA')
            ->delete();

        // Hapus mata pelajaran SMA yang tidak diperlukan (hanya ambil 2 pilihan)
        $smaSubjects = DB::table('subjects')
            ->where('education_level', 'SMA/MA')
            ->where('subject_type', 'Pilihan')
            ->orderBy('subject_number')
            ->get();

        // Hapus mata pelajaran pilihan SMA yang berlebihan (lebih dari 2)
        if ($smaSubjects->count() > 2) {
            $subjectsToDelete = $smaSubjects->skip(2);
            foreach ($subjectsToDelete as $subject) {
                DB::table('subjects')->where('id', $subject->id)->delete();
                $this->line("✅ Deleted {$subject->name} (SMA/MA) - excess");
            }
        }

        // Update subject_number untuk mata pelajaran pilihan SMA yang tersisa
        $remainingSubjects = DB::table('subjects')
            ->where('education_level', 'SMA/MA')
            ->where('subject_type', 'Pilihan')
            ->orderBy('subject_number')
            ->get();

        foreach ($remainingSubjects as $index => $subject) {
            DB::table('subjects')
                ->where('id', $subject->id)
                ->update([
                    'subject_number' => $index + 4, // 4 dan 5 (setelah 3 wajib)
                    'updated_at' => now()
                ]);
        }

        $this->info('✅ Mata pelajaran SMA diperbaiki: 3 wajib + 2 pilihan');

        // Cek hasil
        $mandatoryCount = DB::table('subjects')
            ->where('education_level', 'SMA/MA')
            ->where('subject_type', 'Wajib')
            ->count();

        $optionalCount = DB::table('subjects')
            ->where('education_level', 'SMA/MA')
            ->where('subject_type', 'Pilihan')
            ->count();

        $this->info("📊 SMA/MA - Wajib: {$mandatoryCount}, Pilihan: {$optionalCount}");

        $this->info('🎉 Perbaikan mata pelajaran SMA selesai!');
    }
}
