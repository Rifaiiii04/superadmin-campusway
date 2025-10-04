<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\MajorRecommendation;
use App\Models\StudentChoice;
use App\Models\School;

class OptimizedApiController extends Controller
{
    /**
     * Get majors with caching and optimization
     */
    public function getMajors()
    {
        $cacheKey = 'majors_active_cached';
        $cacheTime = 3600; // 1 hour
        
        return Cache::remember($cacheKey, $cacheTime, function () {
            return MajorRecommendation::select([
                'id', 
                'major_name', 
                'category', 
                'description', 
                'career_prospects',
                'is_active'
            ])
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('major_name')
            ->get()
            ->map(function ($major) {
                return [
                    'id' => $major->id,
                    'major_name' => $major->major_name,
                    'category' => $major->category,
                    'description' => $major->description,
                    'career_prospects' => $major->career_prospects,
                ];
            });
        });
    }

    /**
     * Get major details with caching
     */
    public function getMajorDetails($id)
    {
        $cacheKey = "major_details_{$id}";
        $cacheTime = 3600; // 1 hour
        
        return Cache::remember($cacheKey, $cacheTime, function () use ($id) {
            $major = MajorRecommendation::select([
                'id',
                'major_name',
                'category',
                'description',
                'required_subjects',
                'preferred_subjects',
                'career_prospects',
                'kurikulum_2013_ipa_subjects',
                'kurikulum_2013_ips_subjects',
                'kurikulum_2013_bahasa_subjects'
            ])
            ->where('id', $id)
            ->where('is_active', true)
            ->first();

            if (!$major) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jurusan tidak ditemukan'
                ], 404);
            }

            return [
                'id' => $major->id,
                'major_name' => $major->major_name,
                'category' => $major->category,
                'description' => $major->description,
                'required_subjects' => $major->required_subjects,
                'preferred_subjects' => $major->preferred_subjects,
                'career_prospects' => $major->career_prospects,
                'kurikulum_2013_ipa' => $major->kurikulum_2013_ipa_subjects,
                'kurikulum_2013_ips' => $major->kurikulum_2013_ips_subjects,
                'kurikulum_2013_bahasa' => $major->kurikulum_2013_bahasa_subjects
            ];
        });
    }

    /**
     * Check student's major status with caching
     */
    public function checkMajorStatus($studentId)
    {
        $cacheKey = "major_status_{$studentId}";
        $cacheTime = 60; // 1 minute cache (status can change frequently)
        
        return Cache::remember($cacheKey, $cacheTime, function () use ($studentId) {
            $choice = StudentChoice::where('student_id', $studentId)->first();

            if (!$choice) {
                return [
                    'success' => true,
                    'data' => [
                        'has_choice' => false,
                        'selected_major_id' => null
                    ]
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'has_choice' => true,
                    'selected_major_id' => $choice->major_id
                ]
            ];
        });
    }

    /**
     * Get schools with caching
     */
    public function getSchools()
    {
        $cacheKey = 'schools_cached';
        $cacheTime = 7200; // 2 hours
        
        return Cache::remember($cacheKey, $cacheTime, function () {
            return School::select(['id', 'name', 'npsn'])
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Optimized student login
     */
    public function login(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|max:10',
            'password' => 'required|string'
        ]);

        // Use select specific columns for better performance
        $student = Student::select([
            'id', 'nisn', 'name', 'school_id', 'kelas', 
            'email', 'phone', 'parent_phone', 'password'
        ])
        ->where('nisn', $request->nisn)
        ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'NISN tidak ditemukan'
            ], 404);
        }

        // Verify password
        if (!\Hash::check($request->password, $student->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah'
            ], 401);
        }

        // Get school info with caching
        $school = Cache::remember("school_{$student->school_id}", 3600, function () use ($student) {
            return School::select(['id', 'name'])
                ->where('id', $student->school_id)
                ->first();
        });

        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'Data sekolah tidak ditemukan'
            ], 404);
        }

        // Check if student has choice (optimized query)
        $hasChoice = StudentChoice::where('student_id', $student->id)
            ->select('id')
            ->exists();

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
     * Get student choice with optimization
     */
    public function getStudentChoice($studentId)
    {
        $cacheKey = "student_choice_{$studentId}";
        $cacheTime = 1800; // 30 minutes
        
        return Cache::remember($cacheKey, $cacheTime, function () use ($studentId) {
            $choice = StudentChoice::with([
                'student:id,name,nisn',
                'major:id,major_name,category,description'
            ])
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
                    'choice' => [
                        'id' => $choice->id,
                        'student' => [
                            'id' => $choice->student->id,
                            'name' => $choice->student->name,
                            'nisn' => $choice->student->nisn
                        ],
                        'major' => [
                            'id' => $choice->major->id,
                            'major_name' => $choice->major->major_name,
                            'category' => $choice->major->category,
                            'description' => $choice->major->description
                        ],
                        'created_at' => $choice->created_at
                    ]
                ]
            ]);
        });
    }

    /**
     * Clear cache for specific data
     */
    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all');
        
        switch ($type) {
            case 'majors':
                Cache::forget('majors_active_cached');
                break;
            case 'schools':
                Cache::forget('schools_cached');
                break;
            case 'all':
                Cache::flush();
                break;
        }

        return response()->json([
            'success' => true,
            'message' => "Cache cleared for: {$type}"
        ]);
    }

    /**
     * Health check endpoint
     */
    public function health()
    {
        $startTime = microtime(true);
        
        // Test database connection
        try {
            DB::select('SELECT 1');
            $dbStatus = 'connected';
        } catch (\Exception $e) {
            $dbStatus = 'error';
        }
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;
        
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now(),
            'database' => $dbStatus,
            'response_time' => round($responseTime, 2) . 'ms',
            'memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'memory_peak' => $this->formatBytes(memory_get_peak_usage(true))
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
