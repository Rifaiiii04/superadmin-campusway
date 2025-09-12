<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSMKSubjectsCommand extends Command
{
    protected $signature = 'subjects:fix-smk';
    protected $description = 'Fix SMK subjects to 3 mandatory + Produk/PKK + 1 optional';

    public function handle()
    {
        $this->info('📚 Memperbaiki mata pelajaran SMK menjadi 3 wajib + Produk/PKK + 1 pilihan...');

        // Hapus mapping lama untuk SMK
        DB::table('major_subject_mappings')
            ->where('education_level', 'SMK/MAK')
            ->delete();

        // Hapus mata pelajaran SMK yang tidak diperlukan (hanya ambil 1 pilihan)
        $smkSubjects = DB::table('subjects')
            ->where('education_level', 'SMK/MAK')
            ->where('subject_type', 'Pilihan')
            ->orderBy('subject_number')
            ->get();

        // Hapus mata pelajaran pilihan SMK yang berlebihan (lebih dari 1)
        if ($smkSubjects->count() > 1) {
            $subjectsToDelete = $smkSubjects->skip(1);
            foreach ($subjectsToDelete as $subject) {
                DB::table('subjects')->where('id', $subject->id)->delete();
                $this->line("✅ Deleted {$subject->name} (SMK/MAK) - excess");
            }
        }

        // Update subject_number untuk mata pelajaran pilihan SMK yang tersisa
        $remainingSubjects = DB::table('subjects')
            ->where('education_level', 'SMK/MAK')
            ->where('subject_type', 'Pilihan')
            ->orderBy('subject_number')
            ->get();

        foreach ($remainingSubjects as $index => $subject) {
            DB::table('subjects')
                ->where('id', $subject->id)
                ->update([
                    'subject_number' => $index + 4, // 4 (setelah 3 wajib)
                    'updated_at' => now()
                ]);
        }

        // Pastikan Produk/PKK ada dan benar
        $produkPKK = DB::table('subjects')
            ->where('education_level', 'SMK/MAK')
            ->where('subject_type', 'Pilihan_Wajib')
            ->first();

        if ($produkPKK) {
            DB::table('subjects')
                ->where('id', $produkPKK->id)
                ->update([
                    'subject_number' => 5, // 5 (setelah 3 wajib + 1 pilihan)
                    'updated_at' => now()
                ]);
        }

        $this->info('✅ Mata pelajaran SMK diperbaiki: 3 wajib + 1 pilihan + Produk/PKK');

        // Cek hasil
        $mandatoryCount = DB::table('subjects')
            ->where('education_level', 'SMK/MAK')
            ->where('subject_type', 'Wajib')
            ->count();

        $optionalCount = DB::table('subjects')
            ->where('education_level', 'SMK/MAK')
            ->where('subject_type', 'Pilihan')
            ->count();

        $produkPKKCount = DB::table('subjects')
            ->where('education_level', 'SMK/MAK')
            ->where('subject_type', 'Pilihan_Wajib')
            ->count();

        $this->info("📊 SMK/MAK - Wajib: {$mandatoryCount}, Pilihan: {$optionalCount}, Produk/PKK: {$produkPKKCount}");

        $this->info('🎉 Perbaikan mata pelajaran SMK selesai!');
    }
}
