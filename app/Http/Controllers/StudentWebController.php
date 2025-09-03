<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Student;
use App\Models\MajorRecommendation;
use App\Models\StudentChoice;
use App\Models\School;

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
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'parent_phone' => $student->parent_phone,
                    'school_name' => $school->name,
                    'has_choice' => false
                ]
            ]
        ], 201);
    }

    /**
     * Student login
     */
    public function login(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|max:10',
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

        // Get school for school name
        $school = School::find($student->school_id);
        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'Data sekolah tidak ditemukan'
            ], 404);
        }

        // Verify password (using student's own password)
        if (!Hash::check($request->password, $student->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah'
            ], 401);
        }

        // Check if student has already made a choice
        $hasChoice = StudentChoice::where('student_id', $student->id)->exists();

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'nisn' => $student->nisn,
                    'name' => $student->name,
                    'kelas' => $student->kelas,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'parent_phone' => $student->parent_phone,
                    'school_name' => $school->name,
                    'has_choice' => $hasChoice
                ]
            ]
        ]);
    }

    /**
     * Get available schools for registration
     */
    public function getSchools()
    {
        $schools = School::select('id', 'npsn', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $schools
        ]);
    }

    /**
     * Get all active majors for student selection
     */
    public function getMajors()
    {
        $majors = MajorRecommendation::where('is_active', true)
            ->select('id', 'major_name', 'description', 'career_prospects', 'category')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $majors
        ]);
    }

    /**
     * Get major details with subjects
     */
    public function getMajorDetails($id)
    {
        $major = MajorRecommendation::where('is_active', true)
            ->where('id', $id)
            ->first();

        if (!$major) {
            return response()->json([
                'success' => false,
                'message' => 'Jurusan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $major->id,
                'major_name' => $major->major_name,
                'description' => $major->description,
                'career_prospects' => $major->career_prospects,
                'category' => $major->category,
                'subjects' => [
                    'required' => $major->required_subjects,
                    'preferred' => $major->preferred_subjects,
                    'kurikulum_merdeka' => $major->kurikulum_merdeka_subjects,
                    'kurikulum_2013_ipa' => $major->kurikulum_2013_ipa_subjects,
                    'kurikulum_2013_ips' => $major->kurikulum_2013_ips_subjects,
                    'kurikulum_2013_bahasa' => $major->kurikulum_2013_bahasa_subjects
                ]
            ]
        ]);
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

            // Check if major is active
            $major = MajorRecommendation::where('id', $request->major_id)
                ->where('is_active', true)
                ->first();

            if (!$major) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jurusan tidak aktif atau tidak ditemukan'
                ], 404);
            }

            // Create student choice
            $choice = StudentChoice::create([
                'student_id' => $request->student_id,
                'major_id' => $request->major_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pilihan jurusan berhasil disimpan',
                'data' => [
                    'choice_id' => $choice->id,
                    'major_name' => $major->major_name,
                    'chosen_at' => $choice->created_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Get student's chosen major
     */
    public function getStudentChoice($studentId)
    {
        $choice = StudentChoice::with('major')
            ->where('student_id', $studentId)
            ->first();

        if (!$choice) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa belum memilih jurusan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $choice->id,
                'major' => [
                    'id' => $choice->major->id,
                    'major_name' => $choice->major->major_name,
                    'description' => $choice->major->description,
                    'career_prospects' => $choice->major->career_prospects,
                    'category' => $choice->major->category,
                    'subjects' => [
                        'required' => $choice->major->required_subjects,
                        'preferred' => $choice->major->preferred_subjects,
                        'kurikulum_merdeka' => $choice->major->kurikulum_merdeka_subjects,
                        'kurikulum_2013_ipa' => $choice->major->kurikulum_2013_ipa_subjects,
                        'kurikulum_2013_ips' => $choice->major->kurikulum_2013_ips_subjects,
                        'kurikulum_2013_bahasa' => $choice->major->kurikulum_2013_bahasa_subjects
                    ]
                ],
                'chosen_at' => $choice->created_at
            ]
        ]);
    }

    /**
     * Check if student has chosen a major
     */
    public function checkMajorStatus($studentId)
    {
        $choice = StudentChoice::where('student_id', $studentId)->first();

        if (!$choice) {
            return response()->json([
                'success' => true,
                'data' => [
                    'has_choice' => false,
                    'selected_major_id' => null
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'has_choice' => true,
                'selected_major_id' => $choice->major_id
            ]
        ]);
    }

    /**
     * Change student's major choice
     */
    public function changeMajor(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer|exists:students,id',
            'major_id' => 'required|integer|exists:major_recommendations,id'
        ]);

        // Check if major is active
        $major = MajorRecommendation::where('id', $request->major_id)
            ->where('is_active', true)
            ->first();

        if (!$major) {
            return response()->json([
                'success' => false,
                'message' => 'Jurusan tidak aktif atau tidak ditemukan'
            ], 404);
        }

        // Update or create student choice
        StudentChoice::updateOrCreate(
            ['student_id' => $request->student_id],
            ['major_id' => $request->major_id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Pilihan jurusan berhasil diubah',
            'data' => [
                'major_name' => $major->major_name,
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Test endpoint untuk debugging
     */
    public function testChooseMajor(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Test endpoint working',
                'data' => [
                    'request_data' => $request->all(),
                    'student_exists' => Student::where('id', $request->student_id)->exists(),
                    'major_exists' => MajorRecommendation::where('id', $request->major_id)->exists(),
                    'student_choices_count' => StudentChoice::count(),
                    'table_exists' => Schema::hasTable('student_choices')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Get student profile
     */
    public function getStudentProfile($studentId)
    {
        $student = Student::with(['school', 'majorChoices.major'])
            ->where('id', $studentId)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $student->id,
                'nisn' => $student->nisn,
                'name' => $student->name,
                'kelas' => $student->kelas,
                'email' => $student->email,
                'phone' => $student->phone,
                'parent_phone' => $student->parent_phone,
                'status' => $student->status,
                'school' => [
                    'id' => $student->school->id,
                    'name' => $student->school->name,
                    'npsn' => $student->school->npsn
                ],
                'major_choice' => $student->majorChoices->first() ? [
                    'id' => $student->majorChoices->first()->major->id,
                    'major_name' => $student->majorChoices->first()->major->major_name,
                    'chosen_at' => $student->majorChoices->first()->created_at
                ] : null
            ]
        ]);
    }
}
