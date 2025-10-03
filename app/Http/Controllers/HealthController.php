<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    /**
     * Health check endpoint
     */
    public function check()
    {
        $startTime = microtime(true);
        
        try {
            // Test database connection
            DB::select('SELECT 1');
            $dbStatus = 'OK';
            $dbTime = round((microtime(true) - $startTime) * 1000, 2);
        } catch (\Exception $e) {
            $dbStatus = 'ERROR: ' . $e->getMessage();
            $dbTime = 0;
        }
        
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        
        return response()->json([
            'status' => 'OK',
            'timestamp' => now()->toISOString(),
            'database' => [
                'status' => $dbStatus,
                'response_time' => $dbTime . 'ms'
            ],
            'memory' => [
                'current' => $this->formatBytes($memoryUsage),
                'peak' => $this->formatBytes($memoryPeak)
            ],
            'performance' => [
                'execution_time' => $executionTime . 'ms',
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version()
            ]
        ]);
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
