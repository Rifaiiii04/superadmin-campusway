<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MajorRecommendation;
use App\Models\Subject;
use App\Models\School;

class SchoolLevelMajorController extends Controller
{
    /**
     * Get major recommendations based on school level
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMajorsBySchoolLevel(Request $request)
    {
        try {
            $schoolLevel = $request->input('school_level', 'SMA/MA');
            
            // Validasi school level
            if (!in_array($schoolLevel, ['SMA/MA', 'SMK/MAK'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'School level must be SMA/MA or SMK/MAK'
                ], 400);
            }

            // Dapatkan mata pelajaran berdasarkan jenjang sekolah
            $subjects = Subject::where('education_level', $schoolLevel)
                ->where('is_active', true)
                ->orderBy('subject_number')
                ->get();

            // Dapatkan major recommendations yang sesuai dengan jenjang sekolah
            $majors = MajorRecommendation::where('is_active', true)
                ->get()
                ->map(function ($major) use ($schoolLevel, $subjects) {
                    // Filter mata pelajaran berdasarkan jenjang sekolah
                    $filteredSubjects = $this->filterSubjectsBySchoolLevel($major, $schoolLevel, $subjects);
                    
                    return [
                        'id' => $major->id,
                        'major_name' => $major->major_name,
                        'description' => $major->description,
                        'category' => $major->category,
                        'required_subjects' => $filteredSubjects['required'],
                        'preferred_subjects' => $filteredSubjects['preferred'],
                        'kurikulum_merdeka_subjects' => $filteredSubjects['kurikulum_merdeka'],
                        'kurikulum_2013_ipa_subjects' => $filteredSubjects['kurikulum_2013_ipa'],
                        'kurikulum_2013_ips_subjects' => $filteredSubjects['kurikulum_2013_ips'],
                        'kurikulum_2013_bahasa_subjects' => $filteredSubjects['kurikulum_2013_bahasa'],
                        'career_prospects' => $major->career_prospects,
                        'school_level' => $schoolLevel
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $majors,
                'school_level' => $schoolLevel,
                'total_subjects' => $subjects->count(),
                'message' => "Major recommendations for {$schoolLevel} schools retrieved successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving major recommendations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subjects by school level
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubjectsBySchoolLevel(Request $request)
    {
        try {
            $schoolLevel = $request->input('school_level', 'SMA/MA');
            
            // Validasi school level
            if (!in_array($schoolLevel, ['SMA/MA', 'SMK/MAK'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'School level must be SMA/MA or SMK/MAK'
                ], 400);
            }

            $subjects = Subject::where('education_level', $schoolLevel)
                ->where('is_active', true)
                ->orderBy('subject_number')
                ->get()
                ->map(function ($subject) {
                    return [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'code' => $subject->code,
                        'description' => $subject->description,
                        'education_level' => $subject->education_level,
                        'subject_type' => $subject->subject_type,
                        'subject_number' => $subject->subject_number,
                        'is_required' => $subject->is_required
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $subjects,
                'school_level' => $schoolLevel,
                'total' => $subjects->count(),
                'message' => "Subjects for {$schoolLevel} schools retrieved successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving subjects: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filter subjects based on school level
     * 
     * @param MajorRecommendation $major
     * @param string $schoolLevel
     * @param \Illuminate\Database\Eloquent\Collection $subjects
     * @return array
     */
    private function filterSubjectsBySchoolLevel($major, $schoolLevel, $subjects)
    {
        $availableSubjectNames = $subjects->pluck('name')->toArray();
        
        $filterSubjects = function ($subjectArray) use ($availableSubjectNames) {
            if (!is_array($subjectArray)) {
                return [];
            }
            
            return array_filter($subjectArray, function ($subject) use ($availableSubjectNames) {
                return in_array($subject, $availableSubjectNames);
            });
        };

        return [
            'required' => $filterSubjects($major->required_subjects ?? []),
            'preferred' => $filterSubjects($major->preferred_subjects ?? []),
            'kurikulum_merdeka' => $filterSubjects($major->kurikulum_merdeka_subjects ?? []),
            'kurikulum_2013_ipa' => $filterSubjects($major->kurikulum_2013_ipa_subjects ?? []),
            'kurikulum_2013_ips' => $filterSubjects($major->kurikulum_2013_ips_subjects ?? []),
            'kurikulum_2013_bahasa' => $filterSubjects($major->kurikulum_2013_bahasa_subjects ?? [])
        ];
    }

    /**
     * Get school level statistics
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSchoolLevelStats()
    {
        try {
            $stats = [
                'sma_ma' => [
                    'majors_count' => MajorRecommendation::where('is_active', true)->count(),
                    'subjects_count' => Subject::where('education_level', 'SMA/MA')->where('is_active', true)->count(),
                    'required_subjects' => Subject::where('education_level', 'SMA/MA')->where('subject_type', 'Wajib')->where('is_active', true)->count(),
                    'optional_subjects' => Subject::where('education_level', 'SMA/MA')->where('subject_type', 'Pilihan')->where('is_active', true)->count()
                ],
                'smk_mak' => [
                    'majors_count' => MajorRecommendation::where('is_active', true)->count(),
                    'subjects_count' => Subject::where('education_level', 'SMK/MAK')->where('is_active', true)->count(),
                    'pilihan_subjects' => Subject::where('education_level', 'SMK/MAK')->where('subject_type', 'Pilihan')->where('is_active', true)->count(),
                    'produk_kreatif_subjects' => Subject::where('education_level', 'SMK/MAK')->where('subject_type', 'Produk_Kreatif_Kewirausahaan')->where('is_active', true)->count()
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'School level statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
