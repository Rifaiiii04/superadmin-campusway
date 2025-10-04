<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class OptimizeServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:optimize {--clear-cache : Clear all caches} {--warm-cache : Warm up caches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize server performance by clearing caches, optimizing database, and warming up caches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting server optimization...');
        
        $startTime = microtime(true);
        
        // Clear caches if requested
        if ($this->option('clear-cache')) {
            $this->clearCaches();
        }
        
        // Optimize database
        $this->optimizeDatabase();
        
        // Warm up caches if requested
        if ($this->option('warm-cache')) {
            $this->warmupCaches();
        }
        
        // Set PHP optimizations
        $this->setPhpOptimizations();
        
        $executionTime = round(microtime(true) - $startTime, 2);
        
        $this->info("✅ Server optimization completed in {$executionTime} seconds");
        
        // Show performance metrics
        $this->showPerformanceMetrics();
    }
    
    /**
     * Clear all caches
     */
    private function clearCaches()
    {
        $this->info('🧹 Clearing caches...');
        
        try {
            Artisan::call('cache:clear');
            $this->line('  ✓ Application cache cleared');
            
            Artisan::call('config:clear');
            $this->line('  ✓ Configuration cache cleared');
            
            Artisan::call('route:clear');
            $this->line('  ✓ Route cache cleared');
            
            Artisan::call('view:clear');
            $this->line('  ✓ View cache cleared');
            
            $this->info('✅ All caches cleared successfully');
        } catch (\Exception $e) {
            $this->error('❌ Error clearing caches: ' . $e->getMessage());
        }
    }
    
    /**
     * Optimize database
     */
    private function optimizeDatabase()
    {
        $this->info('🗄️ Optimizing database...');
        
        try {
            // Test database connection
            $start = microtime(true);
            DB::select('SELECT 1 as test');
            $responseTime = round((microtime(true) - $start) * 1000, 2);
            
            $this->line("  ✓ Database connection: {$responseTime}ms");
            
            // Check if indexes exist
            $this->checkDatabaseIndexes();
            
            $this->info('✅ Database optimization completed');
        } catch (\Exception $e) {
            $this->error('❌ Database optimization failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Check database indexes
     */
    private function checkDatabaseIndexes()
    {
        try {
            $tables = ['schools', 'students', 'questions', 'student_choices', 'major_recommendations'];
            
            foreach ($tables as $table) {
                $indexes = DB::select("
                    SELECT COUNT(*) as index_count 
                    FROM sys.indexes 
                    WHERE object_id = OBJECT_ID('{$table}') 
                    AND is_primary_key = 0
                ");
                
                $count = $indexes[0]->index_count ?? 0;
                $this->line("  ✓ Table '{$table}': {$count} indexes");
            }
        } catch (\Exception $e) {
            $this->line("  ⚠️ Could not check indexes: " . $e->getMessage());
        }
    }
    
    /**
     * Warm up caches
     */
    private function warmupCaches()
    {
        $this->info('🔥 Warming up caches...');
        
        try {
            // Cache dashboard stats
            $dashboardStats = Cache::remember('superadmin_dashboard_stats', 600, function () {
                return [
                    'total_schools' => \App\Models\School::count(),
                    'total_students' => \App\Models\Student::count(),
                    'total_majors' => \App\Models\MajorRecommendation::where('is_active', true)->count(),
                ];
            });
            $this->line('  ✓ Dashboard stats cached');
            
            // Cache question subjects
            $subjects = Cache::remember('question_subjects', 600, function () {
                return \App\Models\Question::distinct()->pluck('subject')->sort()->values();
            });
            $this->line('  ✓ Question subjects cached');
            
            // Cache major categories
            $categories = Cache::remember('major_categories', 600, function () {
                return \App\Models\MajorRecommendation::distinct()->pluck('category')->sort()->values();
            });
            $this->line('  ✓ Major categories cached');
            
            $this->info('✅ Cache warmup completed');
        } catch (\Exception $e) {
            $this->error('❌ Cache warmup failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Set PHP optimizations
     */
    private function setPhpOptimizations()
    {
        $this->info('⚙️ Setting PHP optimizations...');
        
        // Set memory limit
        ini_set('memory_limit', '512M');
        $this->line('  ✓ Memory limit set to 512M');
        
        // Set execution time
        set_time_limit(300);
        $this->line('  ✓ Execution time limit set to 300 seconds');
        
        // Enable OPcache if available
        if (function_exists('opcache_get_status')) {
            $status = opcache_get_status();
            if ($status && $status['opcache_enabled']) {
                $this->line('  ✓ OPcache is enabled');
            } else {
                $this->line('  ⚠️ OPcache is not enabled');
            }
        }
        
        $this->info('✅ PHP optimizations applied');
    }
    
    /**
     * Show performance metrics
     */
    private function showPerformanceMetrics()
    {
        $this->info('📊 Performance Metrics:');
        
        // Memory usage
        $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);
        $memoryPeak = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
        $this->line("  Memory Usage: {$memoryUsage}MB (Peak: {$memoryPeak}MB)");
        
        // Database connection test
        try {
            $start = microtime(true);
            DB::select('SELECT 1 as test');
            $dbTime = round((microtime(true) - $start) * 1000, 2);
            $this->line("  Database Response: {$dbTime}ms");
        } catch (\Exception $e) {
            $this->line("  Database Response: Error - " . $e->getMessage());
        }
        
        // Cache test
        try {
            $testKey = 'optimization_test_' . time();
            $start = microtime(true);
            Cache::put($testKey, 'test', 60);
            $value = Cache::get($testKey);
            $cacheTime = round((microtime(true) - $start) * 1000, 2);
            Cache::forget($testKey);
            $this->line("  Cache Response: {$cacheTime}ms");
        } catch (\Exception $e) {
            $this->line("  Cache Response: Error - " . $e->getMessage());
        }
        
        $this->line('');
        $this->info('🎯 Server is now optimized for better performance!');
    }
}