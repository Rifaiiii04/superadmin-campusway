<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OptimizeDatabaseCommand extends Command
{
    protected $signature = 'db:optimize';
    protected $description = 'Optimize database performance';

    public function handle()
    {
        $this->info('🔧 Optimizing database performance...');

        try {
            // Update statistics for better query planning
            $this->info('📊 Updating database statistics...');
            
            // For SQL Server, update statistics on key tables
            $tables = ['schools', 'students', 'major_recommendations', 'student_choices', 'subjects', 'major_subject_mappings'];
            
            foreach ($tables as $table) {
                try {
                    DB::statement("UPDATE STATISTICS [{$table}]");
                    $this->line("✅ Updated statistics for {$table}");
                } catch (\Exception $e) {
                    $this->warn("⚠️ Could not update statistics for {$table}: " . $e->getMessage());
                }
            }

            // Clear query cache if exists
            try {
                DB::statement('DBCC FREEPROCCACHE');
                $this->info('✅ Query cache cleared');
            } catch (\Exception $e) {
                $this->warn("⚠️ Could not clear query cache: " . $e->getMessage());
            }

            $this->info('🎉 Database optimization completed!');
            
        } catch (\Exception $e) {
            $this->error('❌ Database optimization failed: ' . $e->getMessage());
        }
    }
}
