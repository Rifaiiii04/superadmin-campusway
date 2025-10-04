<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Question;
use App\Models\MajorRecommendation;

class ApiController extends Controller
{
    public function getSchools()
    {
        try {
            $schools = School::select('id', 'npsn', 'name', 'school_level')->get();
            return response()->json([
                'success' => true,
                'data' => $schools
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching schools: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getQuestions()
    {
        try {
            $questions = Question::with('options')->get();
            return response()->json([
                'success' => true,
                'data' => $questions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching questions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMajors()
    {
        try {
            $majors = MajorRecommendation::select('id', 'major_name', 'description')->get();
            return response()->json([
                'success' => true,
                'data' => $majors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching majors: ' . $e->getMessage()
            ], 500);
        }
    }

    public function healthCheck()
    {
        return response()->json([
            'status' => 'OK',
            'message' => 'API is working',
            'timestamp' => now()
        ]);
    }
}
