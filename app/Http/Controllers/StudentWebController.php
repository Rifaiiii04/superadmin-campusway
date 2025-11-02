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

            // Format subjects like SchoolDashboardController does
            $majorData = $this->formatMajorWithSubjects($major);

            return response()->json([
                'success' => true,
                'data' => $majorData
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
     * Format major with subjects from database mapping
     */
    private function formatMajorWithSubjects($major)
    {
        try {
            // Parse subjects from JSON if they're stored as strings
            $parseSubjects = function($field) {
                if (is_null($field)) return [];
                if (is_array($field)) return $field;
                if (is_string($field)) {
                    $decoded = json_decode($field, true);
                    return is_array($decoded) ? $decoded : (strlen($field) > 0 ? explode(',', $field) : []);
                }
                return [];
            };

            // Get subjects from database mapping (major_subject_mappings table)
            $mandatorySubjects = [];
            $optionalSubjects = [];
            
            try {
                if (class_exists('\App\Models\MajorSubjectMapping')) {
                    $mappings = \App\Models\MajorSubjectMapping::where('major_id', $major->id)
                        ->where('is_active', 1)
                        ->with('subject')
                        ->get();
                    
                    $mandatorySubjects = $mappings->where('mapping_type', 'wajib')
                        ->pluck('subject.name')
                        ->filter()
                        ->unique()
                        ->values()
                        ->toArray();
                        
                    $optionalSubjects = $mappings->where('mapping_type', 'pilihan')
                        ->pluck('subject.name')
                        ->filter()
                        ->unique()
                        ->values()
                        ->toArray();
                }
            } catch (\Exception $e) {
                Log::warning('Failed to load subject mappings for major ' . $major->id . ': ' . $e->getMessage());
            }

            // If no mappings found, try to get from required_subjects and preferred_subjects fields
            if (empty($mandatorySubjects) && !empty($major->required_subjects)) {
                $mandatorySubjects = $parseSubjects($major->required_subjects);
            }
            
            if (empty($optionalSubjects) && !empty($major->preferred_subjects)) {
                $optionalSubjects = $parseSubjects($major->preferred_subjects);
            }
            
            // If still empty, try optional_subjects field
            if (empty($optionalSubjects) && !empty($major->optional_subjects)) {
                $optionalSubjects = $parseSubjects($major->optional_subjects);
            }

            return [
                'id' => $major->id,
                'major_name' => $major->major_name,
                'description' => $major->description,
                'career_prospects' => $major->career_prospects,
                'category' => $major->category ?? 'Saintek',
                'rumpun_ilmu' => $major->rumpun_ilmu ?? $major->category ?? 'Saintek',
                'is_active' => $major->is_active ?? 1,
                // Format subjects for frontend
                'required_subjects' => $mandatorySubjects,
                'preferred_subjects' => $optionalSubjects,
                'optional_subjects' => $optionalSubjects,
                'kurikulum_merdeka_subjects' => $parseSubjects($major->kurikulum_merdeka_subjects),
                'kurikulum_2013_ipa_subjects' => $parseSubjects($major->kurikulum_2013_ipa_subjects),
                'kurikulum_2013_ips_subjects' => $parseSubjects($major->kurikulum_2013_ips_subjects),
                'kurikulum_2013_bahasa_subjects' => $parseSubjects($major->kurikulum_2013_bahasa_subjects),
                // Also provide in subjects object format for StudentDashboardClient
                'subjects' => [
                    'required' => $mandatorySubjects,
                    'preferred' => $optionalSubjects,
                    'kurikulum_merdeka' => $parseSubjects($major->kurikulum_merdeka_subjects),
                    'kurikulum_2013_ipa' => $parseSubjects($major->kurikulum_2013_ipa_subjects),
                    'kurikulum_2013_ips' => $parseSubjects($major->kurikulum_2013_ips_subjects),
                    'kurikulum_2013_bahasa' => $parseSubjects($major->kurikulum_2013_bahasa_subjects),
                ],
                'created_at' => $major->created_at,
                'updated_at' => $major->updated_at,
            ];
        } catch (\Exception $e) {
            Log::error('Error in formatMajorWithSubjects: ' . $e->getMessage());
            // Return basic major data without subjects
            return [
                'id' => $major->id ?? 0,
                'major_name' => $major->major_name ?? 'Unknown Major',
                'description' => $major->description ?? '',
                'career_prospects' => $major->career_prospects ?? '',
                'category' => $major->category ?? 'Saintek',
                'rumpun_ilmu' => $major->rumpun_ilmu ?? $major->category ?? 'Saintek',
                'required_subjects' => [],
                'preferred_subjects' => [],
                'optional_subjects' => [],
                'kurikulum_merdeka_subjects' => [],
                'kurikulum_2013_ipa_subjects' => [],
                'kurikulum_2013_ips_subjects' => [],
                'kurikulum_2013_bahasa_subjects' => [],
                'subjects' => [
                    'required' => [],
                    'preferred' => [],
                    'kurikulum_merdeka' => [],
                    'kurikulum_2013_ipa' => [],
                    'kurikulum_2013_ips' => [],
                    'kurikulum_2013_bahasa' => [],
                ],
            ];
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

            // Load the choice with major recommendation
            $choice->load('majorRecommendation');

            return response()->json([
                'success' => true,
                'message' => 'Pilihan jurusan berhasil disimpan',
                'data' => [
                    'id' => $choice->id,
                    'student_id' => $choice->student_id,
                    'major_id' => $choice->major_id,
                    'major' => $choice->majorRecommendation ? [
                        'id' => $choice->majorRecommendation->id,
                        'major_name' => $choice->majorRecommendation->major_name,
                        'description' => $choice->majorRecommendation->description,
                        'category' => $choice->majorRecommendation->category,
                    ] : null,
                    'chosen_at' => $choice->created_at,
                    'created_at' => $choice->created_at,
                    'updated_at' => $choice->updated_at
                ]
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
            $choice = StudentChoice::with('majorRecommendation')
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
                'data' => [
                    'id' => $choice->id,
                    'student_id' => $choice->student_id,
                    'major_id' => $choice->major_id,
                    'major' => $choice->majorRecommendation ? [
                        'id' => $choice->majorRecommendation->id,
                        'major_name' => $choice->majorRecommendation->major_name,
                        'description' => $choice->majorRecommendation->description,
                        'category' => $choice->majorRecommendation->category,
                        'rumpun_ilmu' => $choice->majorRecommendation->rumpun_ilmu,
                        'career_prospects' => $choice->majorRecommendation->career_prospects,
                        'required_subjects' => $choice->majorRecommendation->required_subjects,
                        'preferred_subjects' => $choice->majorRecommendation->preferred_subjects,
                        'kurikulum_merdeka_subjects' => $choice->majorRecommendation->kurikulum_merdeka_subjects,
                        'kurikulum_2013_ipa_subjects' => $choice->majorRecommendation->kurikulum_2013_ipa_subjects,
                        'kurikulum_2013_ips_subjects' => $choice->majorRecommendation->kurikulum_2013_ips_subjects,
                        'kurikulum_2013_bahasa_subjects' => $choice->majorRecommendation->kurikulum_2013_bahasa_subjects,
                    ] : null,
                    'chosen_at' => $choice->created_at,
                    'created_at' => $choice->created_at,
                    'updated_at' => $choice->updated_at
                ]
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

            // Load the updated choice with major recommendation
            $choice->load('majorRecommendation');

            return response()->json([
                'success' => true,
                'message' => 'Pilihan jurusan berhasil diubah',
                'data' => [
                    'id' => $choice->id,
                    'student_id' => $choice->student_id,
                    'major_id' => $choice->major_id,
                    'major' => $choice->majorRecommendation ? [
                        'id' => $choice->majorRecommendation->id,
                        'major_name' => $choice->majorRecommendation->major_name,
                        'description' => $choice->majorRecommendation->description,
                        'category' => $choice->majorRecommendation->category,
                    ] : null,
                    'chosen_at' => $choice->created_at,
                    'created_at' => $choice->created_at,
                    'updated_at' => $choice->updated_at
                ]
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
            $schoolId = $request->query('school_id');
            
            $query = \App\Models\TkaSchedule::where('is_active', true)
                ->orderBy('start_date', 'desc');
            
            // Filter by school if provided
            if ($schoolId) {
                $query->where(function($q) use ($schoolId) {
                    $q->whereNull('target_schools')
                      ->orWhereJsonContains('target_schools', $schoolId);
                });
            }
            
            $schedules = $query->get();
            
            return response()->json([
                'success' => true,
                'data' => $schedules
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
            $schoolId = $request->query('school_id');
            $now = now();
            
            $query = \App\Models\TkaSchedule::where('is_active', true)
                ->where('end_date', '>=', $now)
                ->orderBy('start_date', 'asc');
            
            // Filter by school if provided
            if ($schoolId) {
                $query->where(function($q) use ($schoolId) {
                    $q->whereNull('target_schools')
                      ->orWhereJsonContains('target_schools', $schoolId);
                });
            }
            
            $schedules = $query->get();
            
            return response()->json([
                'success' => true,
                'data' => $schedules
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
