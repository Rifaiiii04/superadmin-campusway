<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifySubjectsCommand extends Command
{
    protected $signature = 'subjects:verify';
    protected $description = 'Verify subjects structure';

    public function handle()
    {
        $this->info('📚 Verifikasi struktur mata pelajaran...');

        // SMA/MA Subjects
        $this->info('🎓 SMA/MA Subjects:');
        $smaSubjects = DB::table('subjects')
            ->where('education_level', 'SMA/MA')
            ->orderBy('subject_number')
            ->get();

        foreach ($smaSubjects as $subject) {
            $this->line("  {$subject->subject_number}. {$subject->name} ({$subject->subject_type})");
        }

        $this->info('');

        // SMK/MAK Subjects
        $this->info('🏭 SMK/MAK Subjects:');
        $smkSubjects = DB::table('subjects')
            ->where('education_level', 'SMK/MAK')
            ->orderBy('subject_number')
            ->get();

        foreach ($smkSubjects as $subject) {
            $this->line("  {$subject->subject_number}. {$subject->name} ({$subject->subject_type})");
        }

        $this->info('');

        // Mapping counts
        $smaMappingCount = DB::table('major_subject_mappings')
            ->where('education_level', 'SMA/MA')
            ->count();

        $smkMappingCount = DB::table('major_subject_mappings')
            ->where('education_level', 'SMK/MAK')
            ->count();

        $this->info("📊 Mapping SMA/MA: {$smaMappingCount}");
        $this->info("📊 Mapping SMK/MAK: {$smkMappingCount}");

        $this->info('🎉 Verifikasi selesai!');
    }
}
