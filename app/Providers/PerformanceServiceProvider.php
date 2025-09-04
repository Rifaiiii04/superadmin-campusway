<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Set optimal PHP settings
        $this->setOptimalPhpSettings();
        
        // Configure database for performance
        $this->configureDatabasePerformance();
        
        // Setup query optimization
        $this->setupQueryOptimization();
        
        // Configure caching
        $this->configureCaching();
    }

    /**
     * Set optimal PHP settings
     */
    private function setOptimalPhpSettings(): void
    {
        // Memory and execution time
        ini_set('memory_limit', '1024M');
        set_time_limit(300);
        
        // OPcache settings
        if (function_exists('opcache_get_status')) {
            ini_set('opcache.enable', 1);
            ini_set('opcache.memory_consumption', 256);
            ini_set('opcache.interned_strings_buffer', 16);
            ini_set('opcache.max_accelerated_files', 20000);
            ini_set('opcache.validate_timestamps', 0);
            ini_set('opcache.save_comments', 1);
            ini_set('opcache.fast_shutdown', 1);
        }
        
        // Disable unnecessary features for performance
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
    }

    /**
     * Configure database for performance
     */
    private function configureDatabasePerformance(): void
    {
        // Disable query logging in production
        if (app()->environment('production') || config('performance.optimization.disable_query_log', true)) {
            DB::disableQueryLog();
        }
        
        // Set connection timeout
        $connection = DB::connection();
        if (method_exists($connection, 'getPdo')) {
            try {
                $pdo = $connection->getPdo();
                $pdo->setAttribute(\PDO::ATTR_TIMEOUT, 60);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\Exception $e) {
                Log::warning('Could not set PDO attributes: ' . $e->getMessage());
            }
        }
    }

    /**
     * Setup query optimization
     */
    private function setupQueryOptimization(): void
    {
        // Listen for slow queries
        DB::listen(function ($query) {
            $executionTime = $query->time;
            $slowQueryThreshold = config('performance.database.slow_query_threshold', 1000);
            
            if ($executionTime > $slowQueryThreshold) {
                Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $executionTime . 'ms'
                ]);
            }
        });
    }

    /**
     * Configure caching
     */
    private function configureCaching(): void
    {
        // Enable query result caching for frequently accessed data
        if (config('performance.cache.enable_query_cache', true)) {
            // Cache configuration
            Cache::remember('app_config', config('performance.cache.cache_ttl', 3600), function () {
                return [
                    'majors' => \App\Models\MajorRecommendation::where('is_active', true)->get(),
                    'schools' => \App\Models\School::all(),
                ];
            });
        }
    }
}
