<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class SchoolAuth
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
        // Log untuk debugging
        $logFile = storage_path('logs/middleware_debug.log');
        $logMsg = date('Y-m-d H:i:s') . " === SchoolAuth Middleware Called ===\n";
        $logMsg .= "  URI: " . $request->getRequestUri() . "\n";
        $logMsg .= "  Method: " . $request->method() . "\n";
        file_put_contents($logFile, $logMsg, FILE_APPEND);
        
        error_log('=== SchoolAuth Middleware Called ===');
        error_log('URI: ' . $request->getRequestUri());
        error_log('Method: ' . $request->method());
        
        try {
            // Force JSON response - never redirect
            $request->headers->set('Accept', 'application/json');
            
            $token = $request->header('Authorization');
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - Token exists: " . ($token ? 'YES' : 'NO') . "\n", FILE_APPEND);
            error_log('Token exists: ' . ($token ? 'YES' : 'NO'));
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan'
                ], 401)->header('Content-Type', 'application/json');
            }

            // Remove "Bearer " prefix if exists
            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            // Find the token in database
            $accessToken = PersonalAccessToken::findToken($token);
            
            if (!$accessToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid'
                ], 401)->header('Content-Type', 'application/json');
            }

            // Get the school model from token
            $school = $accessToken->tokenable;
            
            if (!$school || !($school instanceof School)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid untuk sekolah'
                ], 401)->header('Content-Type', 'application/json');
            }

            // Check if token is expired
            if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token sudah expired'
                ], 401)->header('Content-Type', 'application/json');
            }

            // Tambahkan data sekolah ke request
            $request->merge(['school_id' => $school->id]);
            $request->merge(['school' => $school]);
            
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - School added to request: " . ($school->id ?? 'NO ID') . "\n", FILE_APPEND);
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - Calling next middleware/controller\n", FILE_APPEND);

            $response = $next($request);
            
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - Response received, status: " . $response->getStatusCode() . "\n", FILE_APPEND);
            
            // Allow non-JSON responses for file downloads (CSV, Excel, etc.)
            $requestUri = $request->getRequestUri();
            $isFileDownload = strpos($requestUri, '/import-template') !== false || 
                             strpos($requestUri, '/export-') !== false ||
                             strpos($requestUri, '/download') !== false;
            
            // Ensure response is JSON only for API endpoints that should return JSON
            // Allow file downloads (CSV, Excel, etc.) to return their own content type
            if (!$isFileDownload && !$response instanceof \Illuminate\Http\JsonResponse) {
                file_put_contents($logFile, date('Y-m-d H:i:s') . " - ERROR: Response is not JSON for non-download endpoint\n", FILE_APPEND);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response format'
                ], 500)->header('Content-Type', 'application/json');
            }
            
            if ($isFileDownload) {
                file_put_contents($logFile, date('Y-m-d H:i:s') . " - Returning file download response\n", FILE_APPEND);
            } else {
                file_put_contents($logFile, date('Y-m-d H:i:s') . " - Returning JSON response\n", FILE_APPEND);
            }
            return $response;

        } catch (\Exception $e) {
            error_log('=== SchoolAuth Middleware ERROR ===');
            error_log('Error: ' . $e->getMessage());
            error_log('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            error_log('Stack: ' . $e->getTraceAsString());
            
            Log::error('School auth middleware error: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan autentikasi',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500)->header('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            error_log('=== SchoolAuth Middleware FATAL ERROR ===');
            error_log('Error: ' . $e->getMessage());
            error_log('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500)->header('Content-Type', 'application/json');
        }
    }
}
