<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentChoice;
use App\Models\MajorSubjectMapping;
use App\Models\Subject;
use App\Helpers\SMKSubjectHelper;

class StudentSubjectController extends Controller
{
    /**
     * Get subjects for student based on their major choice and school level
     */
    public function getStudentSubjects(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|integer|exists:students,id'
            ]);

            $student = Student::find($request->student_id);
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan'
                ], 404);
            }

            // Get student's choice
            $studentChoice = StudentChoice::with('major')
                ->where('student_id', $student->id)
                ->first();

            if (!$studentChoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa belum memilih jurusan'
                ], 400);
            }

            // Determine education level based on school name or other criteria
            $educationLevel = $this->determineEducationLevel($student);

            // Get all subjects for the major
            $subjects = $this->getSubjectsForEducationLevel(
                $studentChoice->major_id, 
                $educationLevel,
                $studentChoice->major->major_name
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->name,
                        'nisn' => $student->nisn,
                        'kelas' => $student->kelas,
                        'school_name' => $student->school->name ?? 'N/A'
                    ],
                    'major' => [
                        'id' => $studentChoice->major->id,
                        'major_name' => $studentChoice->major->major_name,
                        'description' => $studentChoice->major->description,
                        'category' => $studentChoice->major->category ?? 'Saintek'
                    ],
                    'education_level' => $educationLevel,
                    'subjects' => [
                        'mandatory' => $subjects['mandatory']->map(function($subject) {
                            return [
                                'id' => $subject->id,
                                'name' => $subject->name,
                                'code' => $subject->code,
                                'description' => $subject->description,
                                'type' => 'Wajib',
                                'priority' => $subject->subject_number
                            ];
                        }),
                        'optional' => $subjects['optional']->map(function($subject) {
                            return [
                                'id' => $subject->id,
                                'name' => $subject->name,
                                'code' => $subject->code,
                                'description' => $subject->description,
                                'type' => $subject->subject_type,
                                'priority' => $subject->subject_number
                            ];
                        }),
                        'total_count' => $subjects['total']
                    ],
                    'curriculum' => [
                        'merdeka' => $studentChoice->major->kurikulum_merdeka_subjects ?? [],
                        '2013_ipa' => $studentChoice->major->kurikulum_2013_ipa_subjects ?? [],
                        '2013_ips' => $studentChoice->major->kurikulum_2013_ips_subjects ?? [],
                        '2013_bahasa' => $studentChoice->major->kurikulum_2013_bahasa_subjects ?? []
                    ],
                    'career_prospects' => $studentChoice->major->career_prospects ?? '',
                    'rules' => $this->getEducationLevelRules($educationLevel)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subjects for major and education level (for preview)
     */
    public function getSubjectsForMajor(Request $request)
    {
        try {
            $request->validate([
                'major_id' => 'required|integer|exists:major_recommendations,id',
                'education_level' => 'required|string|in:SMA/MA,SMK/MAK'
            ]);

            $major = \App\Models\MajorRecommendation::find($request->major_id);
            $subjects = $this->getSubjectsForEducationLevel(
                $request->major_id, 
                $request->education_level,
                $major->major_name ?? 'Unknown'
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'education_level' => $request->education_level,
                    'subjects' => [
                        'mandatory' => $subjects['mandatory']->map(function($subject) {
                            return [
                                'id' => $subject->id,
                                'name' => $subject->name,
                                'code' => $subject->code,
                                'description' => $subject->description,
                                'type' => 'Wajib',
                                'priority' => $subject->subject_number
                            ];
                        }),
                        'optional' => $subjects['optional']->map(function($subject) {
                            return [
                                'id' => $subject->id,
                                'name' => $subject->name,
                                'code' => $subject->code,
                                'description' => $subject->description,
                                'type' => $subject->subject_type,
                                'priority' => $subject->subject_number
                            ];
                        }),
                        'total_count' => $subjects['total']
                    ],
                    'curriculum' => [
                        'merdeka' => $major->kurikulum_merdeka_subjects ?? [],
                        '2013_ipa' => $major->kurikulum_2013_ipa_subjects ?? [],
                        '2013_ips' => $major->kurikulum_2013_ips_subjects ?? [],
                        '2013_bahasa' => $major->kurikulum_2013_bahasa_subjects ?? []
                    ],
                    'career_prospects' => $major->career_prospects ?? '',
                    'rules' => $this->getEducationLevelRules($request->education_level)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subjects for education level with proper SMK/SMA logic
     */
    private function getSubjectsForEducationLevel($majorId, $educationLevel, $majorName)
    {
        // Get mandatory subjects (same for both SMA and SMK)
        $mandatorySubjects = Subject::where('subject_type', 'wajib')
            ->where('education_level', $educationLevel)
            ->get();

        // Get optional subjects based on education level
        if ($educationLevel === 'SMK/MAK') {
            // For SMK: Use JSON configuration (1 PKK + 1 optional)
            $optionalSubjectNames = SMKSubjectHelper::getSubjectsForMajor($majorName);
            $optionalSubjects = collect();
            
            foreach ($optionalSubjectNames as $subjectName) {
                $subject = Subject::where('name', $subjectName)
                    ->where('education_level', 'SMK/MAK')
                    ->first();
                if ($subject) {
                    $optionalSubjects->push($subject);
                }
            }
        } else {
            // For SMA: Use database mapping (2 optional subjects)
            $optionalSubjects = MajorSubjectMapping::where('major_id', $majorId)
                ->where('education_level', 'SMA/MA')
                ->where('mapping_type', 'pilihan')
                ->with('subject')
                ->get()
                ->pluck('subject')
                ->filter()
                ->take(2); // Ensure exactly 2 subjects
        }

        return [
            'mandatory' => $mandatorySubjects,
            'optional' => $optionalSubjects,
            'total' => $mandatorySubjects->count() + $optionalSubjects->count()
        ];
    }

    /**
     * Determine education level based on student data
     */
    private function determineEducationLevel($student)
    {
        // Get school name
        $schoolName = $student->school->name ?? '';
        
        // Check if school name contains SMK or MAK
        if (stripos($schoolName, 'SMK') !== false || 
            stripos($schoolName, 'MAK') !== false ||
            stripos($schoolName, 'Sekolah Menengah Kejuruan') !== false) {
            return 'SMK/MAK';
        }
        
        // Default to SMA/MA
        return 'SMA/MA';
    }

    /**
     * Get rules for education level
     */
    private function getEducationLevelRules($educationLevel)
    {
        if ($educationLevel === 'SMK/MAK') {
            return [
                'mandatory_count' => 3,
                'mandatory_subjects' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Matematika'],
                'optional_count' => 2,
                'optional_rules' => [
                    'Pilihan 1: Produk/Projek Kreatif dan Kewirausahaan (Wajib untuk SMK)',
                    'Pilihan 2: Sesuai dengan prodi yang dipilih'
                ],
                'total_subjects' => 5
            ];
        } else {
            return [
                'mandatory_count' => 3,
                'mandatory_subjects' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Matematika'],
                'optional_count' => 2,
                'optional_rules' => [
                    'Pilihan 1: Sesuai dengan prodi yang dipilih',
                    'Pilihan 2: Sesuai dengan prodi yang dipilih'
                ],
                'total_subjects' => 5
            ];
        }
    }
}
