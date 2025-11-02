<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentChoice;
use App\Models\MajorRecommendation;
use Illuminate\Support\Facades\Log;

class SchoolDashboardController extends Controller
{
    /**
     * Get dashboard overview data untuk sekolah (alias for dashboard)
     */
    public function index(Request $request)
    {
        return $this->dashboard($request);
    }

    /**
     * Get dashboard overview data untuk sekolah
     */
    public function dashboard(Request $request)
    {
        try {
            // Get school from middleware (SchoolAuth adds school to request)
            $school = $request->school;
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sekolah tidak ditemukan'
                ], 404);
            }

            // Hitung total siswa
            $totalStudents = Student::where('school_id', $school->id)->count();

            // Hitung siswa yang sudah memilih jurusan
            $studentsWithChoice = StudentChoice::whereHas('student', function($query) use ($school) {
                $query->where('school_id', $school->id);
            })->count();

            // Hitung siswa yang belum memilih jurusan
            $studentsWithoutChoice = $totalStudents - $studentsWithChoice;

            // Get top 5 jurusan yang paling diminati
            $topMajors = StudentChoice::whereHas('student', function($query) use ($school) {
                $query->where('school_id', $school->id);
            })
            ->with('major')
            ->selectRaw('major_id, COUNT(*) as student_count')
            ->groupBy('major_id')
            ->orderBy('student_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($choice) {
                return [
                    'major_id' => $choice->major_id,
                    'major_name' => $choice->major->major_name ?? 'Jurusan Tidak Ditemukan',
                    'category' => $choice->major->category ?? 'Saintek',
                    'student_count' => $choice->student_count
                ];
            });

            // Get data siswa per kelas (jika ada field kelas)
            $studentsByClass = Student::where('school_id', $school->id)
                ->selectRaw('kelas, COUNT(*) as student_count')
                ->groupBy('kelas')
                ->orderBy('kelas')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'npsn' => $school->npsn,
                        'name' => $school->name
                    ],
                    'statistics' => [
                        'total_students' => $totalStudents,
                        'students_with_choice' => $studentsWithChoice,
                        'students_without_choice' => $studentsWithoutChoice,
                        'completion_percentage' => $totalStudents > 0 ? round(($studentsWithChoice / $totalStudents) * 100, 2) : 0
                    ],
                    'top_majors' => $topMajors,
                    'students_by_class' => $studentsByClass
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get school dashboard error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get daftar semua siswa sekolah
     */
    public function students(Request $request)
    {
        try {
            // Get school from middleware (SchoolAuth adds school to request)
            $school = $request->school;
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sekolah tidak ditemukan'
                ], 404);
            }

            $students = Student::where('school_id', $school->id)
                ->with(['studentChoice.major'])
                ->orderBy('name')
                ->get()
                ->map(function($student) {
                    return [
                        'id' => $student->id,
                        'nisn' => $student->nisn,
                        'name' => $student->name,
                        'class' => $student->kelas,
                        'email' => $student->email,
                        'phone' => $student->phone,
                        'parent_phone' => $student->parent_phone,
                        'has_choice' => $student->studentChoice ? true : false,
                        'chosen_major' => $student->studentChoice ? [
                            'id' => $student->studentChoice->major->id,
                            'name' => $student->studentChoice->major->major_name,
                            'description' => $student->studentChoice->major->description,
                            'category' => $student->studentChoice->major->category,
                            'rumpun_ilmu' => $student->studentChoice->major->rumpun_ilmu,
                            'career_prospects' => $student->studentChoice->major->career_prospects,
                        ] : null,
                        'choice_date' => $student->studentChoice ? $student->studentChoice->created_at : null
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'npsn' => $school->npsn,
                        'name' => $school->name
                    ],
                    'students' => $students,
                    'total_students' => $students->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get school students error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get detail siswa tertentu
     */
    public function studentDetail(Request $request, $studentId)
    {
        try {
            $school = $request->school;

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            $student = Student::where('id', $studentId)
                ->where('school_id', $school->id)
                ->first();
            
            // Load relationships separately with error handling
            if ($student) {
                try {
                    $student->load('studentChoice');
                    if ($student->studentChoice) {
                        try {
                            $student->studentChoice->load('majorRecommendation');
                        } catch (\Exception $e) {
                            Log::warning('Failed to load majorRecommendation: ' . $e->getMessage());
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to load studentChoice: ' . $e->getMessage());
                }
            }

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan'
                ], 404);
            }

            $studentData = [
                'id' => $student->id,
                'nisn' => $student->nisn,
                'name' => $student->name,
                'class' => $student->kelas,
                'email' => $student->email,
                'phone' => $student->phone,
                'parent_phone' => $student->parent_phone,
                'created_at' => $student->created_at,
                'updated_at' => $student->updated_at,
                'has_choice' => $student->studentChoice ? true : false
            ];

            // Handle chosen major if student has a choice
            if ($student && isset($student->studentChoice) && $student->studentChoice) {
                try {
                    $major = null;
                    $choiceMajorId = null;
                    
                    // Get major_id safely
                    try {
                        $choiceMajorId = $student->studentChoice->major_id ?? null;
                    } catch (\Exception $e) {
                        Log::warning('Error accessing major_id: ' . $e->getMessage());
                    }
                    
                    // Try to get major from relationship first
                    try {
                        if (property_exists($student->studentChoice, 'majorRecommendation') && $student->studentChoice->majorRecommendation) {
                            $major = $student->studentChoice->majorRecommendation;
                        } elseif (property_exists($student->studentChoice, 'major') && $student->studentChoice->major) {
                            $major = $student->studentChoice->major;
                        }
                    } catch (\Exception $e) {
                        Log::warning('Error accessing major relationship: ' . $e->getMessage());
                    }
                    
                    // If no major from relationship, load directly from database
                    if (!$major && $choiceMajorId) {
                        try {
                            $major = MajorRecommendation::find($choiceMajorId);
                        } catch (\Exception $dbError) {
                            Log::warning('Failed to load major from database: ' . $dbError->getMessage());
                        }
                    }
                    
                    // Process major data
                    if ($major && is_object($major)) {
                        try {
                            $majorData = $this->getMajorWithSubjects($major);
                            if (is_array($majorData)) {
                                $majorData['choice_date'] = $student->studentChoice->created_at ?? null;
                                $studentData['chosen_major'] = $majorData;
                            } else {
                                // Fallback: create basic structure
                                $studentData['chosen_major'] = $this->createBasicMajorData($major, $student->studentChoice);
                            }
                        } catch (\Throwable $subjectError) {
                            Log::error('Error getting major with subjects: ' . $subjectError->getMessage());
                            // Fallback: create basic structure
                            $studentData['chosen_major'] = $this->createBasicMajorData($major, $student->studentChoice);
                        }
                    } elseif ($choiceMajorId) {
                        // Major object failed but we have ID, create minimal structure
                        $studentData['chosen_major'] = [
                            'id' => $choiceMajorId,
                            'name' => 'Unknown Major',
                            'description' => '',
                            'category' => 'Saintek',
                            'choice_date' => $student->studentChoice->created_at ?? null,
                            'required_subjects' => [],
                            'preferred_subjects' => [],
                            'optional_subjects' => []
                        ];
                    }
                } catch (\Throwable $e) {
                    Log::error('Error processing student choice: ' . $e->getMessage());
                    Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
                    // Continue without major data - don't fail the entire request
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'npsn' => $school->npsn,
                        'name' => $school->name
                    ],
                    'student' => $studentData
                ]
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Get student detail error: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Always return JSON, even on error
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server. Silakan coba lagi.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Safely get attribute from model
     */
    private function safeGetAttribute($model, $attribute)
    {
        try {
            if (method_exists($model, 'getAttribute')) {
                return $model->getAttribute($attribute);
            } elseif (property_exists($model, $attribute)) {
                return $model->$attribute ?? null;
            }
            return null;
        } catch (\Throwable $e) {
            Log::warning("Error getting attribute {$attribute}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create basic major data structure (fallback)
     */
    private function createBasicMajorData($major, $studentChoice = null)
    {
        try {
            if (!$major) {
                return [
                    'id' => 0,
                    'name' => 'Unknown',
                    'description' => '',
                    'category' => 'Saintek',
                    'choice_date' => null,
                    'required_subjects' => [],
                    'preferred_subjects' => [],
                    'optional_subjects' => []
                ];
            }
            
            return [
                'id' => $this->safeGetAttribute($major, 'id') ?? 0,
                'name' => $this->safeGetAttribute($major, 'major_name') ?? 'Unknown',
                'description' => $this->safeGetAttribute($major, 'description') ?? '',
                'category' => $this->safeGetAttribute($major, 'category') ?? 'Saintek',
                'choice_date' => $studentChoice ? ($this->safeGetAttribute($studentChoice, 'created_at') ?? null) : null,
                'required_subjects' => [],
                'preferred_subjects' => [],
                'optional_subjects' => []
            ];
        } catch (\Throwable $e) {
            return [
                'id' => 0,
                'name' => 'Unknown',
                'description' => '',
                'category' => 'Saintek',
                'choice_date' => null,
                'required_subjects' => [],
                'preferred_subjects' => [],
                'optional_subjects' => []
            ];
        }
    }

    /**
     * Get major with subjects from database mapping
     */
    private function getMajorWithSubjects($major)
    {
        try {
            // Validate major object
            if (!$major || !is_object($major)) {
                Log::warning('Invalid major object passed to getMajorWithSubjects');
                return [
                    'id' => 0,
                    'name' => 'Unknown Major',
                    'description' => '',
                    'career_prospects' => '',
                    'category' => 'Saintek',
                    'rumpun_ilmu' => 'Saintek',
                    'education_level' => 'SMA/MA',
                    'required_subjects' => [],
                    'preferred_subjects' => [],
                    'optional_subjects' => [],
                    'kurikulum_merdeka_subjects' => [],
                    'kurikulum_2013_ipa_subjects' => [],
                    'kurikulum_2013_ips_subjects' => [],
                    'kurikulum_2013_bahasa_subjects' => []
                ];
            }
            
            $category = $this->safeGetAttribute($major, 'category');
            $rumpunIlmu = $this->safeGetAttribute($major, 'rumpun_ilmu');
            $educationLevel = $this->determineEducationLevel($category ?? $rumpunIlmu ?? 'Saintek');
            
            // Helper function to parse subjects from JSON or string
            $parseSubjects = function($field) {
                if (is_null($field)) return [];
                if (is_array($field)) return $field;
                if (is_string($field)) {
                    $decoded = json_decode($field, true);
                    return is_array($decoded) ? $decoded : (strlen($field) > 0 ? array_filter(array_map('trim', explode(',', $field))) : []);
                }
                return [];
            };
            
            // Get subjects - prioritize direct fields, use mapping as fallback
            $mandatorySubjects = [];
            $optionalSubjects = [];
            
            // First, try to get from direct fields (this is most reliable)
            $requiredSubjectsField = null;
            $preferredSubjectsField = null;
            $optionalSubjectsField = null;
            
            // Safely access properties using getAttribute or array access
            $requiredSubjectsField = null;
            $preferredSubjectsField = null;
            $optionalSubjectsField = null;
            
            try {
                // Use getAttribute if available, otherwise direct property access
                if (method_exists($major, 'getAttribute')) {
                    $requiredSubjectsField = $major->getAttribute('required_subjects');
                    $preferredSubjectsField = $major->getAttribute('preferred_subjects');
                    $optionalSubjectsField = $major->getAttribute('optional_subjects');
                } else {
                    $requiredSubjectsField = isset($major->required_subjects) ? $major->required_subjects : null;
                    $preferredSubjectsField = isset($major->preferred_subjects) ? $major->preferred_subjects : null;
                    $optionalSubjectsField = isset($major->optional_subjects) ? $major->optional_subjects : null;
                }
            } catch (\Throwable $e) {
                Log::warning('Error accessing subject fields: ' . $e->getMessage());
                // Continue with null values
            }
            
            // Parse subjects from fields
            if (!empty($requiredSubjectsField)) {
                try {
                    $mandatorySubjects = $parseSubjects($requiredSubjectsField);
                } catch (\Exception $e) {
                    Log::warning('Error parsing required_subjects: ' . $e->getMessage());
                }
            }
            
            if (!empty($preferredSubjectsField)) {
                try {
                    $optionalSubjects = $parseSubjects($preferredSubjectsField);
                } catch (\Exception $e) {
                    Log::warning('Error parsing preferred_subjects: ' . $e->getMessage());
                }
            }
            
            // If still empty, try optional_subjects field
            if (empty($optionalSubjects) && !empty($optionalSubjectsField)) {
                try {
                    $optionalSubjects = $parseSubjects($optionalSubjectsField);
                } catch (\Exception $e) {
                    Log::warning('Error parsing optional_subjects: ' . $e->getMessage());
                }
            }
            
            // Only try database mapping if we don't have subjects from direct fields
            // This avoids potential errors from missing relationships
            if (empty($mandatorySubjects) && empty($optionalSubjects)) {
                try {
                    if (isset($major->id) && is_numeric($major->id) && class_exists('\App\Models\MajorSubjectMapping')) {
                        try {
                            $mappings = \App\Models\MajorSubjectMapping::where('major_id', $major->id)
                                ->where('is_active', 1)
                                ->get();
                            
                            if ($mappings && $mappings->count() > 0) {
                                // Load subjects separately to avoid relationship issues
                                foreach ($mappings as $mapping) {
                                    if (isset($mapping->subject_id)) {
                                        try {
                                            $subject = \App\Models\Subject::find($mapping->subject_id);
                                            if ($subject && isset($subject->name) && !empty($subject->name)) {
                                                if ($mapping->mapping_type === 'wajib') {
                                                    if (!in_array($subject->name, $mandatorySubjects)) {
                                                        $mandatorySubjects[] = $subject->name;
                                                    }
                                                } elseif ($mapping->mapping_type === 'pilihan') {
                                                    if (!in_array($subject->name, $optionalSubjects)) {
                                                        $optionalSubjects[] = $subject->name;
                                                    }
                                                }
                                            }
                                        } catch (\Exception $subjectError) {
                                            // Skip this subject
                                            continue;
                                        }
                                    }
                                }
                            }
                        } catch (\Exception $mappingError) {
                            Log::warning('Failed to load subject mappings: ' . $mappingError->getMessage());
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Error attempting subject mapping: ' . $e->getMessage());
                }
            }
        
            $majorData = [
                'id' => $this->safeGetAttribute($major, 'id') ?? 0,
                'name' => $this->safeGetAttribute($major, 'major_name') ?? 'Unknown Major',
                'description' => $this->safeGetAttribute($major, 'description') ?? '',
                'career_prospects' => $this->safeGetAttribute($major, 'career_prospects') ?? '',
                'category' => $this->safeGetAttribute($major, 'category') ?? 'Saintek',
                'rumpun_ilmu' => $this->safeGetAttribute($major, 'rumpun_ilmu') ?? $this->safeGetAttribute($major, 'category') ?? 'Saintek',
                'education_level' => $educationLevel,
                'required_subjects' => $mandatorySubjects,
                'preferred_subjects' => $optionalSubjects,
                'optional_subjects' => $optionalSubjects, // Also include optional_subjects for consistency
                'kurikulum_merdeka_subjects' => $parseSubjects($this->safeGetAttribute($major, 'kurikulum_merdeka_subjects')),
                'kurikulum_2013_ipa_subjects' => $parseSubjects($this->safeGetAttribute($major, 'kurikulum_2013_ipa_subjects')),
                'kurikulum_2013_ips_subjects' => $parseSubjects($this->safeGetAttribute($major, 'kurikulum_2013_ips_subjects')),
                'kurikulum_2013_bahasa_subjects' => $parseSubjects($this->safeGetAttribute($major, 'kurikulum_2013_bahasa_subjects'))
            ];
            
            // Log for debugging
            $majorId = $this->safeGetAttribute($major, 'id');
            Log::info('getMajorWithSubjects returning data for major ' . ($majorId ?? 'unknown'), [
                'required_subjects_count' => count($mandatorySubjects),
                'preferred_subjects_count' => count($optionalSubjects),
                'required_subjects' => $mandatorySubjects,
                'preferred_subjects' => $optionalSubjects
            ]);
            
            return $majorData;
        } catch (\Throwable $e) {
            Log::error('Error in getMajorWithSubjects: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            // Return basic major data without subjects using safe method
            try {
                return $this->createBasicMajorData($major ?? null);
            } catch (\Throwable $fallbackError) {
                // Ultimate fallback - return minimal structure
                return [
                    'id' => 0,
                    'name' => 'Unknown Major',
                    'description' => '',
                    'career_prospects' => '',
                    'category' => 'Saintek',
                    'rumpun_ilmu' => 'Saintek',
                    'education_level' => 'SMA/MA',
                    'required_subjects' => [],
                    'preferred_subjects' => [],
                    'optional_subjects' => [],
                    'kurikulum_merdeka_subjects' => [],
                    'kurikulum_2013_ipa_subjects' => [],
                    'kurikulum_2013_ips_subjects' => [],
                    'kurikulum_2013_bahasa_subjects' => []
                ];
            }
        }
    }

    /**
     * Determine education level based on rumpun ilmu
     */
    private function determineEducationLevel($rumpunIlmu)
    {
        $smaRumpun = ['ILMU ALAM', 'ILMU SOSIAL', 'ILMU BUDAYA', 'HUMANIORA', 'ILMU FORMAL'];
        
        if (in_array($rumpunIlmu, $smaRumpun)) {
            return 'SMA/MA';
        } else {
            return 'SMK/MAK';
        }
    }

    /**
     * Get statistik jurusan yang diminati siswa
     */
    public function majorStatistics(Request $request)
    {
        try {
            $school = $request->school;

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            // Get semua jurusan yang dipilih siswa dari sekolah ini
            $majorStats = StudentChoice::whereHas('student', function($query) use ($school) {
                $query->where('school_id', $school->id);
            })
            ->with('major')
            ->selectRaw('major_id, COUNT(*) as student_count')
            ->groupBy('major_id')
            ->orderBy('student_count', 'desc')
            ->get()
            ->map(function($choice) {
                return [
                    'major_id' => $choice->major_id,
                    'major_name' => $choice->major->major_name ?? 'Jurusan Tidak Ditemukan',
                    'description' => $choice->major->description ?? '',
                    'category' => $choice->major->category ?? 'Saintek',
                    'student_count' => $choice->student_count,
                    'percentage' => 0 // Akan dihitung setelah semua data terkumpul
                ];
            });

            // Hitung total siswa yang sudah memilih
            $totalStudentsWithChoice = $majorStats->sum('student_count');

            // Hitung persentase untuk setiap jurusan
            $majorStats = $majorStats->map(function($major) use ($totalStudentsWithChoice) {
                $major['percentage'] = $totalStudentsWithChoice > 0 ? 
                    round(($major['student_count'] / $totalStudentsWithChoice) * 100, 2) : 0;
                return $major;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'npsn' => $school->npsn,
                        'name' => $school->name
                    ],
                    'total_students_with_choice' => $totalStudentsWithChoice,
                    'major_statistics' => $majorStats
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get major statistics error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Tambah siswa baru
     */
    public function addStudent(Request $request)
    {
        try {
            $school = $request->school;
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sekolah tidak ditemukan'
                ], 404);
            }

            $request->validate([
                'nisn' => 'required|string|size:10|unique:students,nisn',
                'name' => 'required|string|max:255',
                'kelas' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'parent_phone' => 'nullable|string|max:20',
                'password' => 'required|string|min:6'
            ]);
            // Cek apakah NISN sudah ada
            $existingStudent = Student::where('nisn', $request->nisn)->first();
            if ($existingStudent) {
                return response()->json([
                    'success' => false,
                    'message' => 'NISN sudah terdaftar'
                ], 400);
            }

            // Buat siswa baru
            $student = Student::create([
                'nisn' => $request->nisn,
                'name' => $request->name,
                'school_id' => $school->id,
                'kelas' => $request->kelas,
                'email' => $request->email,
                'phone' => $request->phone,
                'parent_phone' => $request->parent_phone,
                'password' => bcrypt($request->password),
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil ditambahkan',
                'data' => [
                    'student' => [
                        'id' => $student->id,
                        'nisn' => $student->nisn,
                        'name' => $student->name,
                        'kelas' => $student->kelas,
                        'email' => $student->email,
                        'phone' => $student->phone,
                        'parent_phone' => $student->parent_phone,
                        'status' => $student->status,
                        'created_at' => $student->created_at
                    ]
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Add student error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get daftar siswa yang belum memilih jurusan
     */
    public function studentsWithoutChoice(Request $request)
    {
        try {
            $school = $request->school;

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            $studentsWithoutChoice = Student::where('school_id', $school->id)
                ->whereDoesntHave('studentChoice')
                ->orderBy('name')
                ->get()
                ->map(function($student) {
                    return [
                        'id' => $student->id,
                        'nisn' => $student->nisn,
                        'name' => $student->name,
                        'class' => $student->kelas,
                        'email' => $student->email,
                        'phone' => $student->phone,
                        'parent_phone' => $student->parent_phone
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'npsn' => $school->npsn,
                        'name' => $school->name
                    ],
                    'students_without_choice' => $studentsWithoutChoice,
                    'total_count' => $studentsWithoutChoice->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get students without choice error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Export data siswa dengan detail jurusan dan mata pelajaran
     */
    public function exportStudents(Request $request)
    {
        try {
            $school = $request->school;

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            // Get semua siswa dengan detail jurusan dan mata pelajaran
            $students = Student::where('school_id', $school->id)
                ->with(['studentChoice.major'])
                ->orderBy('name')
                ->get()
                ->map(function($student) {
                    $exportData = [
                        'nama_siswa' => $student->name,
                        'nisn' => $student->nisn,
                        'kelas' => $student->kelas,
                        'email' => $student->email,
                        'no_handphone' => $student->phone,
                        'no_orang_tua' => $student->parent_phone,
                        'status_pilihan_jurusan' => $student->studentChoice ? 'Sudah Memilih' : 'Belum Memilih',
                        'tanggal_memilih' => $student->studentChoice ? $student->studentChoice->created_at->format('d/m/Y H:i') : '-'
                    ];

                    if ($student->studentChoice && $student->studentChoice->major) {
                        $major = $student->studentChoice->major;
                        $exportData = array_merge($exportData, [
                            'nama_jurusan' => $major->major_name,
                            'kategori_jurusan' => $major->category ?? 'Saintek',
                            'prospek_karir' => $major->career_prospects ?? '-',
                            'mata_pelajaran_wajib' => $major->required_subjects ? implode(', ', $major->required_subjects) : '-',
                            'mata_pelajaran_diutamakan' => $major->preferred_subjects ? implode(', ', $major->preferred_subjects) : '-',
                            'mata_pelajaran_kurikulum_merdeka' => $major->kurikulum_merdeka_subjects ? implode(', ', $major->kurikulum_merdeka_subjects) : '-',
                            'mata_pelajaran_kurikulum_2013_ipa' => $major->kurikulum_2013_ipa_subjects ? implode(', ', $major->kurikulum_2013_ipa_subjects) : '-',
                            'mata_pelajaran_kurikulum_2013_ips' => $major->kurikulum_2013_ips_subjects ? implode(', ', $major->kurikulum_2013_ips_subjects) : '-',
                            'mata_pelajaran_kurikulum_2013_bahasa' => $major->kurikulum_2013_bahasa_subjects ? implode(', ', $major->kurikulum_2013_bahasa_subjects) : '-'
                        ]);
                    } else {
                        $exportData = array_merge($exportData, [
                            'nama_jurusan' => '-',
                            'kategori_jurusan' => '-',
                            'prospek_karir' => '-',
                            'mata_pelajaran_wajib' => '-',
                            'mata_pelajaran_diutamakan' => '-',
                            'mata_pelajaran_kurikulum_merdeka' => '-',
                            'mata_pelajaran_kurikulum_2013_ipa' => '-',
                            'mata_pelajaran_kurikulum_2013_ips' => '-',
                            'mata_pelajaran_kurikulum_2013_bahasa' => '-'
                        ]);
                    }

                    return $exportData;
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'npsn' => $school->npsn,
                        'name' => $school->name
                    ],
                    'export_data' => $students,
                    'total_students' => $students->count(),
                    'students_with_choice' => $students->where('status_pilihan_jurusan', 'Sudah Memilih')->count(),
                    'students_without_choice' => $students->where('status_pilihan_jurusan', 'Belum Memilih')->count(),
                    'export_date' => now()->format('d/m/Y H:i:s')
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Export students error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Update data siswa
     */
    public function updateStudent(Request $request, $studentId)
    {
        try {
            $school = $request->user('sanctum');
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Cari siswa yang akan diupdate
            $student = Student::where('id', $studentId)
                ->where('school_id', $school->id)
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan'
                ], 404);
            }

            // Validasi input
            $request->validate([
                'name' => 'required|string|max:255',
                'nisn' => 'required|string|size:10|unique:students,nisn,' . $studentId . ',id',
                'kelas' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'parent_phone' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:6'
            ]);

            // Update data siswa
            $updateData = [
                'name' => $request->name,
                'nisn' => $request->nisn,
                'kelas' => $request->kelas,
                'email' => $request->email,
                'phone' => $request->phone,
                'parent_phone' => $request->parent_phone,
            ];

            // Jika ada password baru, hash password
            if ($request->password) {
                $updateData['password'] = bcrypt($request->password);
            }

            $student->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil diperbarui',
                'data' => [
                    'id' => $student->id,
                    'nisn' => $student->nisn,
                    'name' => $student->name,
                    'class' => $student->kelas,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'parent_phone' => $student->parent_phone,
                    'updated_at' => $student->updated_at
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating student: ' . $e->getMessage());
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
            $school = $request->school;

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
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

    /**
     * Import data siswa dari CSV/Excel
     */
    public function importStudents(Request $request)
    {
        try {
            // Log request data for debugging
            Log::info('Import students request received', [
                'school_id' => $school->id,
                'file_name' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'No file',
                'file_size' => $request->file('file') ? $request->file('file')->getSize() : 0
            ]);
            
            // Custom validation untuk file
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $mimeType = $file->getMimeType();
            
            // Validasi extension dan MIME type
            $allowedExtensions = ['csv', 'xlsx', 'xls'];
            $allowedMimeTypes = [
                'text/csv',
                'text/plain',
                'application/csv',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];
            
            if (!in_array($extension, $allowedExtensions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format file tidak didukung. Gunakan file CSV atau Excel (.xlsx, .xls)',
                    'errors' => ['file' => ['Format file tidak didukung']]
                ], 422);
            }
            
            if (!in_array($mimeType, $allowedMimeTypes)) {
                Log::warning('File MIME type not in allowed list', [
                    'mime_type' => $mimeType,
                    'extension' => $extension,
                    'file_name' => $file->getClientOriginalName()
                ]);
            }
            
            // Validasi school_id
            $request->validate([
                'school_id' => 'required|exists:schools,id'
            ]);

            $school = $request->school;
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }
            
            // Baca file berdasarkan ekstensi
            $data = [];
            if ($extension === 'csv') {
                $data = $this->readCSV($file);
            } elseif (in_array($extension, ['xlsx', 'xls'])) {
                $data = $this->readExcel($file);
            }

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File kosong atau format tidak valid'
                ], 400);
            }

            // Validasi header kolom - support both old and new format
            $requiredColumns = ['nisn', 'name', 'kelas']; // Hanya kolom yang benar-benar wajib
            $optionalColumns = ['email', 'phone', 'parent_phone', 'password'];
            $requiredColumnsNew = ['NISN', 'Nama Lengkap', 'Kelas']; // Hanya kolom yang benar-benar wajib
            $optionalColumnsNew = ['Email', 'No Handphone', 'No Handphone Orang Tua', 'Password'];
            
            $headers = array_keys($data[0]);
            $headersLower = array_map('strtolower', array_map('trim', $headers));
            
            // Check if using new format (with spaces and proper names)
            $isNewFormat = false;
            $missingRequiredColumns = [];
            
            if (count(array_intersect($requiredColumnsNew, $headers)) >= 2) {
                // Using new format
                $isNewFormat = true;
                $missingRequiredColumns = array_diff($requiredColumnsNew, $headers);
            } else {
                // Using old format
                $missingRequiredColumns = array_diff($requiredColumns, $headersLower);
            }
            
            if (!empty($missingRequiredColumns)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kolom yang diperlukan tidak ditemukan: ' . implode(', ', $missingRequiredColumns)
                ], 400);
            }

            $importedCount = 0;
            $skippedCount = 0;
            $errors = [];

            foreach ($data as $index => $row) {
                try {
                    // Normalize data based on format
                    if ($isNewFormat) {
                        // New format with proper column names
                        $normalizedRow = [
                            'nisn' => $row['NISN'] ?? '',
                            'name' => $row['Nama Lengkap'] ?? '',
                            'kelas' => $row['Kelas'] ?? '',
                            'email' => $row['Email'] ?? '',
                            'phone' => $row['No Handphone'] ?? '',
                            'parent_phone' => $row['No Handphone Orang Tua'] ?? '',
                            'password' => $row['Password'] ?? ''
                        ];
                    } else {
                        // Old format - normalize to lowercase
                        $normalizedRow = [
                            'nisn' => $row['nisn'] ?? $row['NISN'] ?? '',
                            'name' => $row['name'] ?? $row['Nama Lengkap'] ?? '',
                            'kelas' => $row['kelas'] ?? $row['Kelas'] ?? '',
                            'email' => $row['email'] ?? $row['Email'] ?? '',
                            'phone' => $row['phone'] ?? $row['No Handphone'] ?? '',
                            'parent_phone' => $row['parent_phone'] ?? $row['No Handphone Orang Tua'] ?? '',
                            'password' => $row['password'] ?? $row['Password'] ?? ''
                        ];
                    }
                    
                    // Validasi data per baris
                    $rowNumber = $index + 2; // +2 karena header di baris 1 dan array dimulai dari 0
                    
                    if (empty($normalizedRow['nisn']) || empty($normalizedRow['name']) || empty($normalizedRow['kelas'])) {
                        $errors[] = "Baris {$rowNumber}: NISN, nama, dan kelas harus diisi";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi NISN format (10 digit)
                    if (!preg_match('/^\d{10}$/', $normalizedRow['nisn'])) {
                        $errors[] = "Baris {$rowNumber}: NISN harus 10 digit angka";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi email format (jika diisi)
                    if (!empty($normalizedRow['email']) && !filter_var($normalizedRow['email'], FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Baris {$rowNumber}: Format email tidak valid";
                        $skippedCount++;
                        continue;
                    }

                    // Normalisasi dan validasi phone format (jika diisi)
                    if (!empty($normalizedRow['phone'])) {
                        $phone = $this->normalizePhoneNumber($normalizedRow['phone']);
                        if (!$phone || !preg_match('/^08\d{8,11}$/', $phone)) {
                            $errors[] = "Baris {$rowNumber}: Format nomor handphone tidak valid (contoh: 081234567890). Gunakan format: =\"081234567890\" di Excel";
                            $skippedCount++;
                            continue;
                        }
                        $normalizedRow['phone'] = $phone;
                    }

                    // Normalisasi dan validasi parent phone format (jika diisi)
                    if (!empty($normalizedRow['parent_phone'])) {
                        $parentPhone = $this->normalizePhoneNumber($normalizedRow['parent_phone']);
                        if (!$parentPhone || !preg_match('/^08\d{8,11}$/', $parentPhone)) {
                            $errors[] = "Baris {$rowNumber}: Format nomor handphone orang tua tidak valid (contoh: 081234567890). Gunakan format: =\"081234567890\" di Excel";
                            $skippedCount++;
                            continue;
                        }
                        $normalizedRow['parent_phone'] = $parentPhone;
                    }

                    // Cek apakah NISN sudah ada
                    $existingStudent = Student::where('nisn', $normalizedRow['nisn'])->first();
                    if ($existingStudent) {
                        $errors[] = "Baris {$rowNumber}: NISN {$normalizedRow['nisn']} sudah terdaftar";
                        $skippedCount++;
                        continue;
                    }

                    // Buat siswa baru
                    Student::create([
                        'nisn' => $normalizedRow['nisn'],
                        'name' => $normalizedRow['name'],
                        'school_id' => $school->id,
                        'kelas' => $normalizedRow['kelas'],
                        'email' => $normalizedRow['email'] ?: null,
                        'phone' => $normalizedRow['phone'] ?: null,
                        'parent_phone' => $normalizedRow['parent_phone'] ?: null,
                        'password' => bcrypt($normalizedRow['password'] ?: 'password123'), // Default password jika kosong
                        'status' => 'active'
                    ]);

                    $importedCount++;

                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                    $skippedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Import selesai. {$importedCount} siswa berhasil diimport, {$skippedCount} siswa dilewati",
                'data' => [
                    'imported_count' => $importedCount,
                    'skipped_count' => $skippedCount,
                    'total_rows' => count($data),
                    'errors' => $errors
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Import students validation failed', [
                'errors' => $e->errors(),
                'school_id' => $school->id ?? 'Not provided',
                'file_name' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'No file'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Import students error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Baca file CSV
     */
    private function readCSV($file)
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');
        
        if ($handle !== false) {
            // Skip BOM if present
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }
            
            // Coba baca dengan semicolon dulu, jika gagal coba dengan comma
            $headers = fgetcsv($handle, 1000, ';');
            if (count($headers) < 3) {
                // Jika tidak cukup kolom dengan semicolon, coba dengan comma
                rewind($handle);
                $bom = fread($handle, 3);
                if ($bom !== "\xEF\xBB\xBF") {
                    rewind($handle);
                }
                $headers = fgetcsv($handle, 1000, ',');
            }
            
            // Clean headers (remove quotes and trim)
            $headers = array_map(function($header) {
                return trim($header, '"');
            }, $headers);
            
            // Tentukan delimiter berdasarkan jumlah kolom yang ditemukan
            $delimiter = count($headers) >= 3 ? ';' : ',';
            
            // Reset file pointer
            rewind($handle);
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }
            
            // Baca ulang dengan delimiter yang benar
            $headers = fgetcsv($handle, 1000, $delimiter);
            $headers = array_map(function($header) {
                return trim($header, '"');
            }, $headers);
            
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (count($row) === count($headers)) {
                    // Clean row data (remove quotes and trim)
                    $cleanRow = array_map(function($field) {
                        return trim($field, '"');
                    }, $row);
                    
                    $data[] = array_combine($headers, $cleanRow);
                }
            }
            
            fclose($handle);
        }
        
        return $data;
    }

    /**
     * Baca file Excel
     */
    private function readExcel($file)
    {
        try {
            $data = [];
            
            // Load PhpSpreadsheet
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getPathname());
            $spreadsheet = $reader->load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Get all rows
            $rows = $worksheet->toArray();
            
            if (empty($rows)) {
                return $data;
            }
            
            // First row as headers - keep original case for new format
            $headers = array_map('trim', $rows[0]);
            
            // Process data rows
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                // Combine headers with row data
                $rowData = [];
                for ($j = 0; $j < count($headers); $j++) {
                    $rowData[$headers[$j]] = isset($row[$j]) ? trim($row[$j]) : '';
                }
                
                $data[] = $rowData;
            }
            
            return $data;
            
        } catch (\Exception $e) {
            Log::error('Excel read error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Download template CSV untuk import data siswa
     */
    public function downloadImportTemplate()
    {
        try {
            // Header CSV dengan nama yang lebih jelas
            $headers = [
                'NISN',
                'Nama Lengkap', 
                'Kelas',
                'Email',
                'No Handphone (Format: ="081234567890")',
                'No Handphone Orang Tua (Format: ="081234567890")',
                'Password'
            ];

            // Data contoh yang lebih lengkap dengan format yang Excel-friendly
            $sampleData = [
                [
                    'NISN' => '1234567890',
                    'Nama Lengkap' => 'Ahmad Rizki Pratama',
                    'Kelas' => 'X IPA 1',
                    'Email' => 'ahmad.rizki@example.com',
                    'No Handphone' => '="081234567890"',
                    'No Handphone Orang Tua' => '="081234567891"',
                    'Password' => 'password123'
                ],
                [
                    'NISN' => '1234567891',
                    'Nama Lengkap' => 'Siti Nurhaliza',
                    'Kelas' => 'X IPA 1',
                    'Email' => 'siti.nurhaliza@example.com',
                    'No Handphone' => '="081234567892"',
                    'No Handphone Orang Tua' => '="081234567893"',
                    'Password' => 'password123'
                ],
                [
                    'NISN' => '1234567892',
                    'Nama Lengkap' => 'Budi Santoso',
                    'Kelas' => 'X IPA 2',
                    'Email' => 'budi.santoso@example.com',
                    'No Handphone' => '="081234567894"',
                    'No Handphone Orang Tua' => '="081234567895"',
                    'Password' => 'password123'
                ],
                [
                    'NISN' => '1234567893',
                    'Nama Lengkap' => 'Dewi Kartika',
                    'Kelas' => 'X IPS 1',
                    'Email' => 'dewi.kartika@example.com',
                    'No Handphone' => '="081234567896"',
                    'No Handphone Orang Tua' => '="081234567897"',
                    'Password' => 'password123'
                ]
            ];

            // Buat CSV content dengan format yang benar untuk Excel
            $csvContent = '';
            
            // BOM untuk UTF-8 (agar Excel mengenali encoding dengan benar)
            $csvContent .= "\xEF\xBB\xBF";
            
            // Header dengan semicolon sebagai delimiter (lebih kompatibel dengan Excel Indonesia)
            $csvContent .= implode(';', $headers) . "\n";
            
            // Data contoh dengan semicolon sebagai delimiter
            foreach ($sampleData as $row) {
                $csvContent .= implode(';', array_map(function($field) {
                    // Escape semicolon dan quotes jika ada
                    $field = str_replace('"', '""', $field);
                    // Wrap dengan quotes jika mengandung semicolon, comma, atau quotes
                    if (strpos($field, ';') !== false || strpos($field, ',') !== false || strpos($field, '"') !== false) {
                        return '"' . $field . '"';
                    }
                    return $field;
                }, $row)) . "\n";
            }

            // Set headers untuk download
            $filename = 'Template_Import_Siswa_' . date('Y-m-d') . '.csv';
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Length', strlen($csvContent))
                ->header('Cache-Control', 'no-cache, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Access-Control-Expose-Headers', 'Content-Disposition, Content-Type, Content-Length');

        } catch (\Exception $e) {
            Log::error('Download template error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh template'
            ], 500);
        }
    }

    /**
     * Normalize phone number from various formats
     */
    private function normalizePhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }

        // Remove all non-digit characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // Handle Excel formula format: ="081234567890"
        if (strpos($phone, '=') !== false) {
            $phone = str_replace(['=', '"', "'"], '', $phone);
        }
        
        // Remove leading +62 and replace with 0
        if (substr($phone, 0, 3) === '+62') {
            $phone = '0' . substr($phone, 3);
        }
        
        // Remove leading 62 and add 0
        if (substr($phone, 0, 2) === '62' && strlen($phone) >= 10) {
            $phone = '0' . substr($phone, 2);
        }
        
        // Ensure it starts with 08
        if (substr($phone, 0, 1) === '8' && strlen($phone) >= 10) {
            $phone = '0' . $phone;
        }
        
        // Remove any remaining non-digit characters
        $phone = preg_replace('/[^\d]/', '', $phone);
        
        // Validate length (10-13 digits for Indonesian mobile numbers)
        if (strlen($phone) < 10 || strlen($phone) > 13) {
            return null;
        }
        
        return $phone;
    }

    /**
     * Get classes list for the school
     */
    public function getClasses(Request $request)
    {
        try {
            $school = $request->school;
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data sekolah tidak ditemukan'
                ], 404);
            }

            // Get unique classes from students data
            $classes = Student::where('school_id', $school->id)
                ->select('kelas')
                ->distinct()
                ->whereNotNull('kelas')
                ->where('kelas', '!=', '')
                ->orderBy('kelas')
                ->pluck('kelas')
                ->map(function($kelas) {
                    return [
                        'name' => $kelas,
                        'value' => $kelas
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'classes' => $classes,
                    'total_classes' => $classes->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get classes error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar kelas'
            ], 500);
        }
    }

    /**
     * Get import rules and requirements
     */
    public function getImportRules()
    {
        try {
            $rules = [
                'required_columns' => [
                    'nisn' => 'NISN siswa (wajib, 10 digit, unik)',
                    'name' => 'Nama lengkap siswa (wajib)',
                    'kelas' => 'Kelas siswa (wajib)',
                    'email' => 'Email siswa (opsional)',
                    'phone' => 'Nomor handphone siswa (opsional)',
                    'parent_phone' => 'Nomor handphone orang tua (opsional)',
                    'password' => 'Password default (opsional, default: password123)'
                ],
                'file_requirements' => [
                    'max_size' => '10MB',
                    'allowed_formats' => ['CSV', 'Excel (.xlsx, .xls)'],
                    'encoding' => 'UTF-8'
                ],
                'validation_rules' => [
                    'nisn' => 'Harus 10 digit, unik di sistem',
                    'name' => 'Tidak boleh kosong',
                    'kelas' => 'Tidak boleh kosong',
                    'email' => 'Format email yang valid (jika diisi)',
                    'phone' => 'Format nomor handphone yang valid (jika diisi)',
                    'parent_phone' => 'Format nomor handphone yang valid (jika diisi)'
                ],
                'tips' => [
                    'Gunakan template yang disediakan untuk memastikan format yang benar',
                    'Pastikan NISN unik dan tidak duplikat',
                    'Untuk nomor handphone di Excel: gunakan format =\"081234567890\" agar angka 0 di depan tidak hilang',
                    'Alternatif: format kolom sebagai "Text" sebelum memasukkan nomor handphone',
                    'Gunakan koma (,) sebagai pemisah kolom',
                    'Gunakan tanda kutip ganda (") untuk data yang mengandung koma',
                    'Hapus baris kosong di akhir file',
                    'Pastikan encoding file adalah UTF-8'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $rules
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get import rules error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil aturan import'
            ], 500);
        }
    }
}
