<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\School;
use Illuminate\Support\Facades\Log;

class SchoolAuthController extends Controller
{
    /**
     * Show school login page
     */
    public function showLogin()
    {
        return inertia('School/Login');
    }

    /**
     * Login sekolah menggunakan NPSN dan password
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'npsn' => 'required|string|size:8',
                'password' => 'required|string|min:6'
            ], [
                'npsn.required' => 'NPSN harus diisi',
                'npsn.size' => 'NPSN harus 8 digit',
                'password.required' => 'Password harus diisi',
                'password.min' => 'Password minimal 6 karakter'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $npsn = $request->npsn;
            $password = $request->password;

            // Cari sekolah berdasarkan NPSN
            $school = School::where('npsn', $npsn)->first();

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'NPSN tidak ditemukan'
                ], 404);
            }

            // Verifikasi password
            if (!Hash::check($password, $school->password_hash)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password salah'
                ], 401);
            }

            // Generate token sederhana (bisa diganti dengan JWT atau Sanctum)
            $token = base64_encode($school->id . '|' . time() . '|' . $school->npsn);

            Log::info('School login successful', [
                'school_id' => $school->id,
                'npsn' => $school->npsn,
                'school_name' => $school->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'token' => $token,
                    'school' => [
                        'id' => $school->id,
                        'npsn' => $school->npsn,
                        'name' => $school->name
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('School login error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'debug' => [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Logout sekolah
     */
    public function logout(Request $request)
    {
        try {
            // Untuk implementasi sederhana, kita hanya return success
            // Dalam implementasi yang lebih kompleks, token bisa di-blacklist
            
            Log::info('School logout', [
                'school_id' => $request->school_id ?? 'unknown'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil'
            ], 200);

        } catch (\Exception $e) {
            Log::error('School logout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get profile sekolah
     */
    public function profile(Request $request)
    {
        try {
            $school = School::find($request->school_id);

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $school->id,
                    'npsn' => $school->npsn,
                    'name' => $school->name,
                    'created_at' => $school->created_at,
                    'updated_at' => $school->updated_at
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get school profile error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Update password sekolah
     */
    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|string|min:6'
            ], [
                'new_password.required' => 'Password baru harus diisi',
                'new_password.min' => 'Password minimal 6 karakter'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $school = School::find($request->school_id);

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            // Update password
            $school->password_hash = Hash::make($request->new_password);
            $school->save();

            Log::info('School password updated successfully', [
                'school_id' => $school->id,
                'npsn' => $school->npsn,
                'school_name' => $school->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Update school password error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }
}
