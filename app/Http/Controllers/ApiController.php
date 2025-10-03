<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Student;
use App\Models\Question;
use App\Models\MajorRecommendation;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    /**
     * Get all schools
     */
    public function getSchools(): JsonResponse
    {
        try {
            $schools = Cache::remember('api_schools', 300, function () {
                return School::select('id', 'npsn', 'name', 'created_at')
                    ->orderBy('name')
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $schools,
                'total' => $schools->count()
            ]);
        } catch (\Exception $e) {
            Log::error('API Schools Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data sekolah',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all questions
     */
    public function getQuestions(): JsonResponse
    {
        try {
            $questions = Cache::remember('api_questions', 600, function () {
                return Question::with(['questionOptions'])
                    ->where('is_active', true)
                    ->orderBy('question_number')
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $questions,
                'total' => $questions->count()
            ]);
        } catch (\Exception $e) {
            Log::error('API Questions Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pertanyaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all results
     */
    public function getResults(Request $request): JsonResponse
    {
        try {
            $query = Result::with(['student.school', 'student.studentChoices.major'])
                ->orderBy('created_at', 'desc');

            // Filter by school if provided
            if ($request->has('school_id')) {
                $query->whereHas('student', function ($q) use ($request) {
                    $q->where('school_id', $request->school_id);
                });
            }

            // Filter by date range if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            $results = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $results->items(),
                'pagination' => [
                    'current_page' => $results->currentPage(),
                    'last_page' => $results->lastPage(),
                    'per_page' => $results->perPage(),
                    'total' => $results->total()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('API Results Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data hasil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all major recommendations
     */
    public function getMajors(): JsonResponse
    {
        try {
            $majors = Cache::remember('api_majors', 600, function () {
                return MajorRecommendation::where('is_active', true)
                    ->orderBy('major_name')
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $majors,
                'total' => $majors->count()
            ]);
        } catch (\Exception $e) {
            Log::error('API Majors Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jurusan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get school statistics
     */
    public function getSchoolStats(Request $request): JsonResponse
    {
        try {
            $schoolId = $request->get('school_id');
            
            if (!$schoolId) {
                return response()->json([
                    'success' => false,
                    'message' => 'School ID diperlukan'
                ], 400);
            }

            $school = School::findOrFail($schoolId);
            
            $stats = Cache::remember("school_stats_{$schoolId}", 300, function () use ($schoolId) {
                return [
                    'total_students' => Student::where('school_id', $schoolId)->count(),
                    'students_with_choice' => Student::where('school_id', $schoolId)
                        ->whereHas('studentChoices')
                        ->count(),
                    'students_without_choice' => Student::where('school_id', $schoolId)
                        ->whereDoesntHave('studentChoices')
                        ->count(),
                    'total_results' => Result::whereHas('student', function ($q) use ($schoolId) {
                        $q->where('school_id', $schoolId);
                    })->count(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => $school,
                    'statistics' => $stats
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('API School Stats Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik sekolah',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Health check endpoint
     */
    public function health(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'API is healthy',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    }

    /**
     * Clear cache endpoint
     */
    public function clearCache(): JsonResponse
    {
        try {
            Cache::flush();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('API Clear Cache Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
