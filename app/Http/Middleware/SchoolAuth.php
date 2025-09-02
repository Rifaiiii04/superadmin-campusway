<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\Log;

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

            // Decode token sederhana
            $decodedToken = base64_decode($token);
            if (!$decodedToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid'
                ], 401);
            }

            $tokenParts = explode('|', $decodedToken);
            if (count($tokenParts) !== 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format token tidak valid'
                ], 401);
            }

            $schoolId = $tokenParts[0];
            $timestamp = $tokenParts[1];
            $npsn = $tokenParts[2];

            // Cek apakah token masih valid (24 jam)
            $tokenAge = time() - $timestamp;
            if ($tokenAge > 86400) { // 24 jam dalam detik
                return response()->json([
                    'success' => false,
                    'message' => 'Token sudah expired'
                ], 401);
            }

            // Verifikasi sekolah masih ada
            $school = School::find($schoolId);
            if (!$school || $school->npsn !== $npsn) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 401);
            }

            // Tambahkan data sekolah ke request
            $request->merge(['school_id' => $schoolId]);
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
