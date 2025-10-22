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
        try {
            $token = $request->header('Authorization');
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan'
                ], 401);
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
                ], 401);
            }

            // Get the school model from token
            $school = $accessToken->tokenable;
            
            if (!$school || !($school instanceof School)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid untuk sekolah'
                ], 401);
            }

            // Check if token is expired
            if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token sudah expired'
                ], 401);
            }

            // Tambahkan data sekolah ke request
            $request->merge(['school_id' => $school->id]);
            $request->merge(['school' => $school]);

            return $next($request);

        } catch (\Exception $e) {
            Log::error('School auth middleware error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan autentikasi'
            ], 500);
        }
    }
}
