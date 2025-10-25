<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ApiResponseService
{
    /**
     * Create optimized API response
     */
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $statusCode = 200,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge([
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
            ], $meta),
        ];

        // Compress response in production
        if (config('app.env') === 'production') {
            $response = self::compressResponse($response);
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Create error response
     */
    public static function error(
        string $message = 'Error',
        int $statusCode = 400,
        mixed $errors = null,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'meta' => array_merge([
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
            ], $meta),
        ];

        // Log errors in production
        if (config('app.env') === 'production' && $statusCode >= 500) {
            Log::error('API Error Response', [
                'message' => $message,
                'status_code' => $statusCode,
                'errors' => $errors,
            ]);
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Create paginated response
     */
    public static function paginated(
        mixed $data,
        int $currentPage,
        int $perPage,
        int $total,
        int $lastPage,
        string $message = 'Success'
    ): JsonResponse {
        return self::success($data, $message, 200, [
            'pagination' => [
                'current_page' => $currentPage,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
                'from' => ($currentPage - 1) * $perPage + 1,
                'to' => min($currentPage * $perPage, $total),
            ],
        ]);
    }

    /**
     * Compress response data
     */
    private static function compressResponse(array $response): array
    {
        // Remove null values
        $response = array_filter($response, fn($value) => $value !== null);
        
        // Remove empty arrays
        $response = array_filter($response, fn($value) => !is_array($value) || !empty($value));
        
        return $response;
    }

    /**
     * Cache API response
     */
    public static function cacheResponse(
        string $key,
        callable $callback,
        int $ttl = 300
    ): mixed {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Get cached response or execute callback
     */
    public static function getCachedOrExecute(
        string $cacheKey,
        callable $callback,
        int $ttl = 300
    ): JsonResponse {
        $cached = Cache::get($cacheKey);
        
        if ($cached) {
            return response()->json($cached);
        }

        $response = $callback();
        $responseData = $response->getData(true);
        
        Cache::put($cacheKey, $responseData, $ttl);
        
        return $response;
    }

    /**
     * Clear API cache
     */
    public static function clearApiCache(string $pattern = '*'): void
    {
        $keys = Cache::getRedis()->keys("api:{$pattern}");
        if (!empty($keys)) {
            Cache::getRedis()->del($keys);
        }
    }

    /**
     * Add performance headers
     */
    public static function addPerformanceHeaders(JsonResponse $response): JsonResponse
    {
        $response->headers->set('X-API-Version', config('app.version', '1.0.0'));
        $response->headers->set('X-Response-Time', microtime(true) - LARAVEL_START);
        $response->headers->set('X-Memory-Usage', memory_get_usage(true));
        
        return $response;
    }

    /**
     * Health check response
     */
    public static function healthCheck(): JsonResponse
    {
        $status = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'version' => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
            'database' => self::checkDatabaseConnection(),
            'cache' => self::checkCacheConnection(),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
        ];

        return self::success($status, 'System is healthy', 200);
    }

    /**
     * Check database connection
     */
    private static function checkDatabaseConnection(): array
    {
        try {
            \DB::connection()->getPdo();
            return ['status' => 'connected', 'driver' => \DB::connection()->getDriverName()];
        } catch (\Exception $e) {
            return ['status' => 'disconnected', 'error' => $e->getMessage()];
        }
    }

    /**
     * Check cache connection
     */
    private static function checkCacheConnection(): array
    {
        try {
            Cache::put('health_check', 'ok', 10);
            $value = Cache::get('health_check');
            return ['status' => $value === 'ok' ? 'connected' : 'error', 'driver' => config('cache.default')];
        } catch (\Exception $e) {
            return ['status' => 'disconnected', 'error' => $e->getMessage()];
        }
    }
}
