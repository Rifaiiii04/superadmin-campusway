<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiOptimization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Add performance headers
        $response = $next($request);
        
        // Add performance monitoring headers
        $response->headers->set('X-Response-Time', round((microtime(true) - $startTime) * 1000, 2) . 'ms');
        $response->headers->set('X-Memory-Usage', round(memory_get_usage(true) / 1024 / 1024, 2) . 'MB');
        $response->headers->set('X-Peak-Memory', round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB');
        
        // Add caching headers for GET requests
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            $this->addCachingHeaders($response, $request);
        }
        
        // Log slow requests in production
        if (config('app.env') === 'production') {
            $responseTime = (microtime(true) - $startTime) * 1000;
            if ($responseTime > 2000) { // Log requests slower than 2 seconds
                Log::warning('Slow API request detected', [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'response_time' => $responseTime . 'ms',
                    'memory_usage' => memory_get_usage(true),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }
        
        return $response;
    }
    
    /**
     * Add caching headers to response
     */
    private function addCachingHeaders(Response $response, Request $request): void
    {
        $path = $request->path();
        
        // Different cache strategies based on endpoint
        if (str_contains($path, 'api/web/schools') || str_contains($path, 'api/web/majors')) {
            // Static data - cache for 1 hour
            $response->headers->set('Cache-Control', 'public, max-age=3600, s-maxage=3600');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
        } elseif (str_contains($path, 'api/web/health')) {
            // Health check - cache for 30 seconds
            $response->headers->set('Cache-Control', 'public, max-age=30, s-maxage=30');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 30) . ' GMT');
        } elseif (str_contains($path, 'api/school/students') || str_contains($path, 'api/school/dashboard')) {
            // Dynamic data - cache for 5 minutes
            $response->headers->set('Cache-Control', 'private, max-age=300, s-maxage=300');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 300) . ' GMT');
        } else {
            // Default - no cache
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }
    }
}
