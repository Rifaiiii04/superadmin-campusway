<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckSubjectsCommand extends Command
{
    protected $signature = 'subjects:check';
    protected $description = 'Check and fix subjects data';

    public function handle()
    {
        $this->info('📚 Mengecek data mata pelajaran...');

        // Cek semua mata pelajaran
        $allSubjects = DB::table('subjects')->get();
        $this->info("Total subjects: {$allSubjects->count()}");

        // Cek education levels
        $levels = DB::table('subjects')
            ->select('education_level')
            ->distinct()
            ->get();

        $this->info("Education levels found:");
        foreach ($levels as $level) {
            $count = DB::table('subjects')
                ->where('education_level', $level->education_level)
                ->count();
            $this->line("- {$level->education_level}: {$count} subjects");
        }

        // Cek subject types
        $types = DB::table('subjects')
            ->select('subject_type')
            ->distinct()
            ->get();

        $this->info("Subject types found:");
        foreach ($types as $type) {
            $count = DB::table('subjects')
                ->where('subject_type', $type->subject_type)
                ->count();
            $this->line("- {$type->subject_type}: {$count} subjects");
        }

        // Cek beberapa mata pelajaran spesifik
        $mandatorySubjects = ['Bahasa Indonesia', 'Bahasa Inggris', 'Matematika'];
        foreach ($mandatorySubjects as $subjectName) {
            $subjects = DB::table('subjects')
                ->where('name', $subjectName)
                ->get();
            
            $this->info("{$subjectName}:");
            foreach ($subjects as $subject) {
                $this->line("  - ID: {$subject->id}, Level: {$subject->education_level}, Type: {$subject->subject_type}");
            }
        }

        $this->info('🎉 Pengecekan selesai!');
    }
}
