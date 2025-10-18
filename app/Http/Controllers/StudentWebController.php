<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\School;
use App\Models\MajorRecommendation;
use App\Models\StudentChoice;
use App\Models\StudentSubject;
use App\Models\Subject;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StudentWebController extends Controller
{
    /**
     * Student registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|max:10|unique:students,nisn',
            'name' => 'required|string|max:255',
            'npsn_sekolah' => 'required|string|max:8|exists:schools,npsn',
            'nama_sekolah' => 'required|string|max:255',
            'kelas' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6'
        ]);

        // Find school by NPSN
        $school = School::where('npsn', $request->npsn_sekolah)->first();
        
        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'NPSN sekolah tidak ditemukan'
            ], 404);
        }

        // Verify school name matches NPSN
        if (strtolower(trim($school->name)) !== strtolower(trim($request->nama_sekolah))) {
            return response()->json([
                'success' => false,
                'message' => 'NPSN tidak cocok dengan nama sekolah'
            ], 400);
        }

        // Create student with hashed password
        $student = Student::create([
            'nisn' => $request->nisn,
            'name' => $request->name,
            'school_id' => $school->id,
            'kelas' => $request->kelas,
            'email' => $request->email,
            'phone' => $request->phone,
            'parent_phone' => $request->parent_phone,
            'password' => Hash::make($request->password),
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'nisn' => $student->nisn,
                    'name' => $student->name,
                    'kelas' => $student->kelas,
                    'class' => $student->kelas, // Alias for compatibility
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'parent_phone' => $student->parent_phone,
                    'school_id' => $student->school_id,
                    'school_name' => $school->name,
                    'has_choice' => false // Will be updated later
                ]
            ]
        ], 201);
    }

    /**
     * Student login
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'nisn' => 'required|string',
                'password' => 'required|string'
            ]);

            // Find student by NISN
            $student = Student::where('nisn', $request->nisn)->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'NISN tidak ditemukan'
                ], 404);
            }

            // Check password
            if (!Hash::check($request->password, $student->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password salah'
                ], 401);
            }

            // Check if student is active
            if ($student->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun siswa tidak aktif'
                ], 403);
            }

            // Generate token (simple implementation)
            $token = base64_encode($student->id . '|' . time() . '|' . $student->nisn);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'student' => [
                        'id' => $student->id,
                        'nisn' => $student->nisn,
                        'name' => $student->name,
                        'kelas' => $student->kelas,
                        'class' => $student->kelas, // Alias for compatibility
                        'email' => $student->email,
                        'phone' => $student->phone,
                        'parent_phone' => $student->parent_phone,
                        'school_id' => $student->school_id,
                        'school_name' => $student->school ? $student->school->name : null,
                        'has_choice' => false // Will be updated later
                    ],
                    'token' => $token
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Student login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get available schools
     */
    public function getSchools()
    {
        try {
            $schools = School::select('id', 'npsn', 'name', 'school_level')
                ->where('school_level', '!=', null)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $schools
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get schools error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar sekolah'
            ], 500);
        }
    }

    /**
     * Get all active majors
     */
    public function getMajors()
    {
        try {
            $majors = MajorRecommendation::where('is_active', 1)
                ->select('id', 'major_name', 'description', 'category')
                ->orderBy('major_name')
                ->get();

            // Add rumpun_ilmu field for frontend compatibility
            $majors = $majors->map(function ($major) {
                $major->rumpun_ilmu = $major->category;
                return $major;
            });

            return response()->json([
                'success' => true,
                'data' => $majors
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get majors error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar jurusan'
            ], 500);
        }
    }

    /**
     * Get major details
     */
    public function getMajorDetails($id)
    {
        try {
            $major = MajorRecommendation::find($id);

            if (!$major) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jurusan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $major
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get major details error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail jurusan'
            ], 500);
        }
    }

    /**
     * Student choose major
     */
    public function chooseMajor(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|integer|exists:students,id',
                'major_id' => 'required|integer|exists:major_recommendations,id'
            ]);

            // Check if student already has a choice
            $existingChoice = StudentChoice::where('student_id', $request->student_id)->first();
            
            if ($existingChoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa sudah memilih jurusan sebelumnya'
                ], 400);
            }

            // Create new choice
            $choice = StudentChoice::create([
                'student_id' => $request->student_id,
                'major_id' => $request->major_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pilihan jurusan berhasil disimpan',
                'data' => $choice
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Choose major error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pilihan jurusan'
            ], 500);
        }
    }

    /**
     * Get student's chosen major
     */
    public function getStudentChoice($studentId)
    {
        try {
            $choice = StudentChoice::with('major')
                ->where('student_id', $studentId)
                ->first();

            if (!$choice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa belum memilih jurusan',
                    'data' => null
                ], 200);
            }

            return response()->json([
                'success' => true,
                'data' => $choice
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get student choice error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil pilihan jurusan'
            ], 500);
        }
    }

    /**
     * Check student's major status
     */
    public function checkMajorStatus($studentId)
    {
        try {
            $choice = StudentChoice::where('student_id', $studentId)->first();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'has_choice' => $choice ? true : false,
                    'choice_id' => $choice ? $choice->id : null,
                    'major_id' => $choice ? $choice->major_id : null
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Check major status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa status jurusan'
            ], 500);
        }
    }

    /**
     * Change student's major choice
     */
    public function changeMajor(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|integer|exists:students,id',
                'major_id' => 'required|integer|exists:major_recommendations,id'
            ]);

            $choice = StudentChoice::where('student_id', $request->student_id)->first();
            
            if (!$choice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa belum memilih jurusan'
                ], 404);
            }

            $choice->update(['major_id' => $request->major_id]);

            return response()->json([
                'success' => true,
                'message' => 'Pilihan jurusan berhasil diubah',
                'data' => $choice
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Change major error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah pilihan jurusan'
            ], 500);
        }
    }

    /**
     * Get student profile
     */
    public function getStudentProfile($studentId)
    {
        try {
            $student = Student::with(['school', 'studentChoice.major'])
                ->find($studentId);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $student
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get student profile error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil profil siswa'
            ], 500);
        }
    }

    /**
     * Get TKA schedules
     */
    public function getTkaSchedules(Request $request)
    {
        try {
            // For now, return empty array since TKA schedules functionality is not implemented yet
            return response()->json([
                'success' => true,
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get TKA schedules error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal ArahPotensi'
            ], 500);
        }
    }

    /**
     * Get upcoming TKA schedules
     */
    public function getUpcomingTkaSchedules(Request $request)
    {
        try {
            // For now, return empty array since TKA schedules functionality is not implemented yet
            return response()->json([
                'success' => true,
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get upcoming TKA schedules error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal ArahPotensi mendatang'
            ], 500);
        }
    }

    /**
     * Test endpoint untuk debugging
     */
    public function testChooseMajor(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Test endpoint working',
            'data' => $request->all()
        ], 200);
    }
}
