<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceOptimization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Disable query logging untuk performa
        if (config('performance.optimization.disable_query_log', true)) {
            DB::disableQueryLog();
        }

        // Set memory limit
        if (config('performance.memory.limit')) {
            ini_set('memory_limit', config('performance.memory.limit'));
        }

        // Set execution time limit
        if (config('performance.timeout.max_execution_time')) {
            set_time_limit(config('performance.timeout.max_execution_time'));
        }

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $next($request);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsed = $endMemory - $startMemory;

        // Log slow requests
        if ($executionTime > config('performance.database.slow_query_threshold', 1000)) {
            Log::warning('Slow API request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time' => $executionTime . 'ms',
                'memory_used' => $this->formatBytes($memoryUsed),
                'user_agent' => $request->userAgent(),
            ]);
        }

        // Add performance headers
        $response->headers->set('X-Execution-Time', $executionTime . 'ms');
        $response->headers->set('X-Memory-Used', $this->formatBytes($memoryUsed));

        return $response;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
