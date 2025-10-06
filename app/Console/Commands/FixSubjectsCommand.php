<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSubjectsCommand extends Command
{
    protected $signature = 'subjects:fix';
    protected $description = 'Fix mandatory subjects according to requirements';

    public function handle()
    {
        $this->info('📚 Memperbaiki mata pelajaran wajib...');

        // Update mata pelajaran yang sudah ada
        $this->info('🔄 Memperbarui mata pelajaran yang sudah ada...');

        // Update untuk SMA/MA
        DB::table('subjects')
            ->where('name', 'Bahasa Indonesia')
            ->where('education_level', 'SMA/MA')
            ->update([
                'subject_type' => 'Wajib',
                'subject_number' => 1,
                'is_required' => true,
                'updated_at' => now()
            ]);

        DB::table('subjects')
            ->where('name', 'Bahasa Inggris')
            ->where('education_level', 'SMA/MA')
            ->update([
                'subject_type' => 'Wajib',
                'subject_number' => 2,
                'is_required' => true,
                'updated_at' => now()
            ]);

        DB::table('subjects')
            ->where('name', 'Matematika')
            ->where('education_level', 'SMA/MA')
            ->update([
                'subject_type' => 'Wajib',
                'subject_number' => 3,
                'is_required' => true,
                'updated_at' => now()
            ]);

        // Update untuk SMK/MAK
        DB::table('subjects')
            ->where('name', 'Bahasa Indonesia')
            ->where('education_level', 'SMK/MAK')
            ->update([
                'subject_type' => 'Wajib',
                'subject_number' => 1,
                'is_required' => true,
                'updated_at' => now()
            ]);

        DB::table('subjects')
            ->where('name', 'Bahasa Inggris')
            ->where('education_level', 'SMK/MAK')
            ->update([
                'subject_type' => 'Wajib',
                'subject_number' => 2,
                'is_required' => true,
                'updated_at' => now()
            ]);

        DB::table('subjects')
            ->where('name', 'Matematika')
            ->where('education_level', 'SMK/MAK')
            ->update([
                'subject_type' => 'Wajib',
                'subject_number' => 3,
                'is_required' => true,
                'updated_at' => now()
            ]);

        $this->info('✅ Mata pelajaran wajib diperbarui!');

        // Cek hasil
        $mandatorySubjects = DB::table('subjects')
            ->where('subject_type', 'Wajib')
            ->orderBy('education_level')
            ->orderBy('subject_number')
            ->get();

        $this->info('📊 Mata pelajaran wajib setelah update:');
        foreach ($mandatorySubjects as $subject) {
            $this->line("- {$subject->name} ({$subject->education_level}) - {$subject->subject_type}");
        }

        $this->info('🎉 Perbaikan selesai!');
    }
}
