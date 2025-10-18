<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentChoice;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SchoolAuthController extends Controller
{
    /**
     * Show school login page
     */
    public function showLogin()
    {
        return Inertia::render('School/Login');
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

            // Verifikasi password - handle both hashed and plain text passwords
            $passwordValid = false;
            if (str_starts_with($school->password, '$2y$')) {
                // Password is hashed
                $passwordValid = Hash::check($password, $school->password);
            } else {
                // Password is plain text
                $passwordValid = $password === $school->password;
            }
            
            if (!$passwordValid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password salah'
                ], 401);
            }

            // Generate Sanctum token
            try {
                $token = $school->createToken('school-token')->plainTextToken;
            } catch (\Exception $e) {
                Log::error('Token creation error: ' . $e->getMessage());
                throw $e;
            }

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
            $school->password = Hash::make($request->new_password);
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

    /**
     * Get dashboard data for school
     */
    public function dashboard(Request $request)
    {
        try {
            $school = $request->user('sanctum');
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Get total students
            $totalStudents = \App\Models\Student::where('school_id', $school->id)->count();
            
            // Get students with choices
            $studentsWithChoices = \App\Models\StudentChoice::whereHas('student', function($query) use ($school) {
                $query->where('school_id', $school->id);
            })->count();
            
            // Get students without choices
            $studentsWithoutChoices = $totalStudents - $studentsWithChoices;
            
            // Get recent students
            $recentStudents = \App\Models\Student::where('school_id', $school->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'nisn', 'created_at']);

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'name' => $school->name,
                        'npsn' => $school->npsn,
                        'address' => $school->address,
                        'phone' => $school->phone,
                        'email' => $school->email,
                    ],
                    'statistics' => [
                        'total_students' => $totalStudents,
                        'students_with_choices' => $studentsWithChoices,
                        'students_without_choices' => $studentsWithoutChoices,
                    ],
                    'recent_students' => $recentStudents
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('School dashboard error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get all students for the school
     */
    public function getStudents(Request $request)
    {
        try {
            $school = $request->user('sanctum');
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $students = \App\Models\Student::where('school_id', $school->id)
                ->with(['chosenMajor'])
                ->orderBy('name')
                ->get()
                ->map(function($student) {
                    return [
                        'id' => $student->id,
                        'nisn' => $student->nisn,
                        'name' => $student->name,
                        'class' => $student->kelas,
                        'kelas' => $student->kelas,
                        'email' => $student->email,
                        'phone' => $student->phone,
                        'parent_phone' => $student->parent_phone,
                        'has_choice' => $student->chosenMajor ? true : false,
                        'chosen_major' => $student->chosenMajor ? [
                            'id' => $student->chosenMajor->id,
                            'name' => $student->chosenMajor->major_name,
                            'description' => $student->chosenMajor->description,
                            'career_prospects' => $student->chosenMajor->career_prospects,
                            'rumpun_ilmu' => $student->chosenMajor->rumpun_ilmu,
                        ] : null,
                        'choice_date' => $student->chosenMajor ? $student->chosenMajor->created_at : null,
                        'created_at' => $student->created_at,
                        'updated_at' => $student->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'name' => $school->name,
                        'npsn' => $school->npsn,
                    ],
                    'students' => $students,
                    'total_students' => $students->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get students error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get major statistics for the school
     */
    public function getMajorStatistics(Request $request)
    {
        try {
            $school = $request->user('sanctum');
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Get students with choices
            $studentsWithChoices = \App\Models\StudentChoice::whereHas('student', function($query) use ($school) {
                $query->where('school_id', $school->id);
            })->with(['major'])->get();

            // Group by major
            $majorStats = $studentsWithChoices->groupBy('major_id')->map(function($choices, $majorId) {
                $major = $choices->first()->major;
                return [
                    'major_id' => $majorId,
                    'major_name' => $major->name,
                    'description' => $major->description,
                    'category' => $major->rumpun_ilmu ?? 'Umum',
                    'student_count' => $choices->count(),
                    'percentage' => 0 // Will be calculated on frontend
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'name' => $school->name,
                        'npsn' => $school->npsn,
                    ],
                    'total_students_with_choice' => $studentsWithChoices->count(),
                    'major_statistics' => $majorStats
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get major statistics error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Delete student
     */
    public function deleteStudent(Request $request, $studentId)
    {
        try {
            $school = $request->user('sanctum');
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Cari siswa yang akan dihapus
            $student = Student::where('id', $studentId)
                ->where('school_id', $school->id)
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan'
                ], 404);
            }

            // Hapus pilihan jurusan siswa terlebih dahulu (jika ada)
            StudentChoice::where('student_id', $studentId)->delete();

            // Hapus siswa
            $student->delete();

            Log::info('Student deleted successfully', [
                'student_id' => $studentId,
                'school_id' => $school->id,
                'student_name' => $student->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error deleting student: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }
}
