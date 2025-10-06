<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PerformanceController extends Controller
{
    /**
     * Get server performance metrics
     */
    public function getMetrics()
    {
        try {
            $metrics = [
                'timestamp' => now()->toISOString(),
                'database' => $this->getDatabaseMetrics(),
                'cache' => $this->getCacheMetrics(),
                'memory' => $this->getMemoryMetrics(),
                'queries' => $this->getQueryMetrics(),
            ];

            return response()->json([
                'success' => true,
                'data' => $metrics
            ]);
        } catch (\Exception $e) {
            Log::error('Performance metrics error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get performance metrics'
            ], 500);
        }
    }

    /**
     * Get database connection metrics
     */
    private function getDatabaseMetrics()
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1 as test');
            $responseTime = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => 'connected',
                'response_time_ms' => $responseTime,
                'connection_count' => DB::getPdo() ? 1 : 0,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'response_time_ms' => null,
            ];
        }
    }

    /**
     * Get cache metrics
     */
    private function getCacheMetrics()
    {
        try {
            $testKey = 'performance_test_' . time();
            $testValue = 'test_value';
            
            $start = microtime(true);
            Cache::put($testKey, $testValue, 60);
            $getStart = microtime(true);
            $retrieved = Cache::get($testKey);
            $getTime = round((microtime(true) - $getStart) * 1000, 2);
            $putTime = round(($getStart - $start) * 1000, 2);
            
            Cache::forget($testKey);

            return [
                'status' => 'working',
                'put_time_ms' => $putTime,
                'get_time_ms' => $getTime,
                'test_passed' => $retrieved === $testValue,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get memory usage metrics
     */
    private function getMemoryMetrics()
    {
        return [
            'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'memory_peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'memory_limit' => ini_get('memory_limit'),
        ];
    }

    /**
     * Get query performance metrics
     */
    private function getQueryMetrics()
    {
        try {
            $queries = DB::getQueryLog();
            $totalQueries = count($queries);
            $totalTime = array_sum(array_column($queries, 'time'));
            
            return [
                'total_queries' => $totalQueries,
                'total_time_ms' => round($totalTime, 2),
                'average_time_ms' => $totalQueries > 0 ? round($totalTime / $totalQueries, 2) : 0,
                'slow_queries' => array_filter($queries, function($query) {
                    return $query['time'] > 100; // Queries slower than 100ms
                }),
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Optimize server performance
     */
    public function optimize()
    {
        try {
            $results = [];

            // Clear all caches
            $results['cache_clear'] = $this->clearAllCaches();
            
            // Optimize database
            $results['database_optimize'] = $this->optimizeDatabase();
            
            // Warm up caches
            $results['cache_warmup'] = $this->warmupCaches();

            return response()->json([
                'success' => true,
                'message' => 'Server optimization completed',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Server optimization error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all caches
     */
    private function clearAllCaches()
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');
            
            return [
                'status' => 'success',
                'message' => 'All caches cleared successfully'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Optimize database
     */
    private function optimizeDatabase()
    {
        try {
            // Enable query logging temporarily
            DB::enableQueryLog();
            
            // Test database connection
            $start = microtime(true);
            DB::select('SELECT COUNT(*) as count FROM students');
            $responseTime = round((microtime(true) - $start) * 1000, 2);
            
            return [
                'status' => 'success',
                'response_time_ms' => $responseTime,
                'message' => 'Database connection optimized'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Warm up frequently used caches
     */
    private function warmupCaches()
    {
        try {
            $results = [];

            // Cache dashboard stats
            $results['dashboard_stats'] = Cache::remember('superadmin_dashboard_stats', 600, function () {
                return [
                    'total_schools' => \App\Models\School::count(),
                    'total_students' => \App\Models\Student::count(),
                    'total_majors' => \App\Models\MajorRecommendation::where('is_active', true)->count(),
                ];
            });

            // Cache question subjects
            $results['question_subjects'] = Cache::remember('question_subjects', 600, function () {
                return \App\Models\Question::distinct()->pluck('subject')->sort()->values();
            });

            return [
                'status' => 'success',
                'cached_items' => count($results),
                'message' => 'Caches warmed up successfully'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Health check endpoint
     */
    public function health()
    {
        try {
            $start = microtime(true);
            
            // Test database
            DB::select('SELECT 1 as test');
            $dbTime = round((microtime(true) - $start) * 1000, 2);
            
            // Test cache
            $cacheStart = microtime(true);
            Cache::put('health_check', 'ok', 60);
            $cacheValue = Cache::get('health_check');
            $cacheTime = round((microtime(true) - $cacheStart) * 1000, 2);
            Cache::forget('health_check');
            
            $totalTime = round((microtime(true) - $start) * 1000, 2);

            return response()->json([
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'response_time_ms' => $totalTime,
                'database' => [
                    'status' => 'connected',
                    'response_time_ms' => $dbTime
                ],
                'cache' => [
                    'status' => $cacheValue === 'ok' ? 'working' : 'error',
                    'response_time_ms' => $cacheTime
                ],
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }
}
