<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestTimeout
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
        // Set execution time limit based on request type
        $timeLimit = $this->getTimeLimit($request);
        
        // Set memory limit
        ini_set('memory_limit', '512M');
        
        // Set execution time
        set_time_limit($timeLimit);
        
        // Log slow requests
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $executionTime = microtime(true) - $startTime;
        
        // Log requests that take longer than 2 seconds
        if ($executionTime > 2) {
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time' => round($executionTime, 2),
                'memory_usage' => memory_get_usage(true),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ]);
        }
        
        return $response;
    }
    
    /**
     * Get time limit based on request type
     */
    private function getTimeLimit(Request $request)
    {
        $path = $request->path();
        
        // API endpoints get more time
        if (str_starts_with($path, 'api/')) {
            return 60; // 60 seconds for API
        }
        
        // Admin pages get moderate time
        if (str_starts_with($path, 'super-admin/')) {
            return 30; // 30 seconds for admin
        }
        
        // Default time limit
        return 20; // 20 seconds for other requests
    }
}
