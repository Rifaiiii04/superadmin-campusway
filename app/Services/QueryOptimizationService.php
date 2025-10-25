<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class QueryOptimizationService
{
    /**
     * Cache query results with TTL
     */
    public static function cacheQuery(string $key, callable $query, int $ttl = 300): mixed
    {
        return Cache::remember($key, $ttl, $query);
    }
    
    /**
     * Optimize database queries with eager loading
     */
    public static function optimizeQueries(): void
    {
        // Enable query logging only in development
        if (config('app.env') === 'development') {
            DB::enableQueryLog();
        }
    }
    
    /**
     * Get slow queries and log them
     */
    public static function logSlowQueries(): void
    {
        if (config('app.env') === 'production') {
            $queries = DB::getQueryLog();
            $slowQueries = array_filter($queries, function ($query) {
                return $query['time'] > 2000; // Queries slower than 2 seconds
            });
            
            if (!empty($slowQueries)) {
                Log::warning('Slow database queries detected', [
                    'queries' => $slowQueries,
                    'count' => count($slowQueries),
                ]);
            }
        }
    }
    
    /**
     * Clear query cache
     */
    public static function clearQueryCache(): void
    {
        Cache::tags(['queries'])->flush();
    }
    
    /**
     * Get database performance metrics
     */
    public static function getPerformanceMetrics(): array
    {
        $queries = DB::getQueryLog();
        $totalTime = array_sum(array_column($queries, 'time'));
        $queryCount = count($queries);
        
        return [
            'total_queries' => $queryCount,
            'total_time' => $totalTime,
            'average_time' => $queryCount > 0 ? $totalTime / $queryCount : 0,
            'slow_queries' => count(array_filter($queries, fn($q) => $q['time'] > 1000)),
        ];
    }
    
    /**
     * Optimize specific queries
     */
    public static function optimizeSchoolQueries(): array
    {
        return [
            'schools_with_students' => self::cacheQuery(
                'schools_with_students_count',
                fn() => DB::table('schools')
                    ->leftJoin('students', 'schools.id', '=', 'students.school_id')
                    ->select('schools.*', DB::raw('COUNT(students.id) as student_count'))
                    ->groupBy('schools.id')
                    ->get(),
                3600 // Cache for 1 hour
            ),
            
            'majors_with_details' => self::cacheQuery(
                'majors_with_details',
                fn() => DB::table('majors')
                    ->select('id', 'name', 'description', 'category', 'requirements')
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(),
                1800 // Cache for 30 minutes
            ),
        ];
    }
    
    /**
     * Clear specific cache keys
     */
    public static function clearSpecificCache(array $keys): void
    {
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
    
    /**
     * Warm up cache with frequently accessed data
     */
    public static function warmUpCache(): void
    {
        // Warm up schools cache
        self::cacheQuery('schools_list', fn() => DB::table('schools')->select('id', 'name', 'address')->get(), 3600);
        
        // Warm up majors cache
        self::cacheQuery('majors_list', fn() => DB::table('majors')->select('id', 'name', 'category')->where('is_active', true)->get(), 1800);
        
        // Warm up TKA schedules cache
        self::cacheQuery('tka_schedules_upcoming', fn() => DB::table('tka_schedules')
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->limit(10)
            ->get(), 600);
    }
}
