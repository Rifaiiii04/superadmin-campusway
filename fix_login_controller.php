<?php
// Script untuk memperbaiki SchoolAuthController dengan logging yang lebih detail

$controllerContent = '<?php

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
        return inertia(\'School/Login\');
    }

    /**
     * Login sekolah menggunakan NPSN dan password
     */
    public function login(Request $request)
    {
        Log::info(\'School login attempt started\', [
            \'npsn\' => $request->npsn,
            \'ip\' => $request->ip(),
            \'user_agent\' => $request->userAgent()
        ]);

        try {
            $validator = Validator::make($request->all(), [
                \'npsn\' => \'required|string|size:8\',
                \'password\' => \'required|string|min:6\'
            ], [
                \'npsn.required\' => \'NPSN harus diisi\',
                \'npsn.size\' => \'NPSN harus 8 digit\',
                \'password.required\' => \'Password harus diisi\',
                \'password.min\' => \'Password minimal 6 karakter\'
            ]);

            if ($validator->fails()) {
                Log::warning(\'School login validation failed\', [
                    \'errors\' => $validator->errors()->toArray()
                ]);
                
                return response()->json([
                    \'success\' => false,
                    \'message\' => \'Validasi gagal\',
                    \'errors\' => $validator->errors()
                ], 422);
            }

            $npsn = $request->npsn;
            $password = $request->password;

            Log::info(\'Looking for school with NPSN\', [\'npsn\' => $npsn]);

            // Cari sekolah berdasarkan NPSN
            $school = School::where(\'npsn\', $npsn)->first();

            if (!$school) {
                Log::warning(\'School not found\', [\'npsn\' => $npsn]);
                return response()->json([
                    \'success\' => false,
                    \'message\' => \'NPSN tidak ditemukan\'
                ], 404);
            }

            Log::info(\'School found\', [
                \'school_id\' => $school->id,
                \'school_name\' => $school->school_name
            ]);

            // Verifikasi password
            if (!Hash::check($password, $school->password)) {
                Log::warning(\'Invalid password for school\', [
                    \'school_id\' => $school->id,
                    \'npsn\' => $npsn
                ]);
                
                return response()->json([
                    \'success\' => false,
                    \'message\' => \'Password salah\'
                ], 401);
            }

            Log::info(\'Password verified for school\', [\'school_id\' => $school->id]);

            // Generate token
            $tokenData = [
                \'school_id\' => $school->id,
                \'timestamp\' => time()
            ];
            $token = base64_encode(json_encode($tokenData));

            Log::info(\'Token generated for school\', [
                \'school_id\' => $school->id,
                \'token_length\' => strlen($token)
            ]);

            return response()->json([
                \'success\' => true,
                \'message\' => \'Login berhasil\',
                \'data\' => [
                    \'token\' => $token,
                    \'school\' => [
                        \'id\' => $school->id,
                        \'school_name\' => $school->school_name,
                        \'npsn\' => $school->npsn,
                        \'address\' => $school->address,
                        \'phone\' => $school->phone,
                        \'email\' => $school->email
                    ]
                ]
            ], 200);

        } catch (\\Exception $e) {
            Log::error(\'School login error\', [
                \'error\' => $e->getMessage(),
                \'file\' => $e->getFile(),
                \'line\' => $e->getLine(),
                \'trace\' => $e->getTraceAsString()
            ]);

            return response()->json([
                \'success\' => false,
                \'message\' => \'Terjadi kesalahan server\',
                \'error\' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout sekolah
     */
    public function logout(Request $request)
    {
        Log::info(\'School logout attempt\');
        
        return response()->json([
            \'success\' => true,
            \'message\' => \'Logout berhasil\'
        ], 200);
    }

    /**
     * Get school profile
     */
    public function profile(Request $request)
    {
        try {
            $school = $request->school;
            
            if (!$school) {
                return response()->json([
                    \'success\' => false,
                    \'message\' => \'Sekolah tidak ditemukan\'
                ], 404);
            }

            return response()->json([
                \'success\' => true,
                \'data\' => [
                    \'id\' => $school->id,
                    \'school_name\' => $school->school_name,
                    \'npsn\' => $school->npsn,
                    \'address\' => $school->address,
                    \'phone\' => $school->phone,
                    \'email\' => $school->email
                ]
            ], 200);

        } catch (\\Exception $e) {
            Log::error(\'School profile error\', [
                \'error\' => $e->getMessage(),
                \'trace\' => $e->getTraceAsString()
            ]);

            return response()->json([
                \'success\' => false,
                \'message\' => \'Terjadi kesalahan server\'
            ], 500);
        }
    }
}';

// Write the file
file_put_contents('SchoolAuthController.php', $controllerContent);

echo "SchoolAuthController.php created with enhanced logging\n";
echo "Upload this file to VPS to fix the login issues\n";
?>
