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
    public function studentDetail(Request $request, $id)
    {
        // Use error_log for immediate logging (bypasses Laravel log system)
        error_log('=== studentDetail METHOD CALLED ===');
        error_log('ID: ' . $id);
        error_log('Request URI: ' . $request->getRequestUri());
        error_log('Request Method: ' . $request->method());
        
        try {
            $studentId = $id; // Use $id from route parameter
            
            Log::info('studentDetail called', [
                'student_id' => $studentId,
                'id_parameter' => $id,
                'request_method' => $request->method(),
                'request_uri' => $request->getRequestUri(),
                'all_request_params' => $request->all(),
                'route_params' => $request->route()->parameters()
            ]);
            
            error_log('After Log::info');

            // Validate studentId
            if (!is_numeric($studentId)) {
                Log::warning('Invalid student ID', ['student_id' => $studentId]);
                return response()->json([
                    'success' => false,
                    'message' => 'ID siswa tidak valid'
                ], 400);
            }

            // Get school from middleware
            $school = $request->school ?? null;
            
            if (!$school || !is_object($school)) {
                Log::error('School not found in request', [
                    'student_id' => $studentId,
                    'school_type' => gettype($school),
                    'has_school' => isset($request->school)
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }
            
            Log::info('School found', ['school_id' => $school->id ?? 'unknown']);

            $schoolId = null;
            try {
                $schoolId = $school->id ?? null;
            } catch (\Exception $e) {
                Log::error('Error getting school ID: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'ID sekolah tidak valid'
                ], 400);
            }

            if (!$schoolId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID sekolah tidak valid'
                ], 400);
            }

            // Get student with separate query
            $student = null;
            try {
                $student = Student::where('id', (int)$studentId)
                    ->where('school_id', (int)$schoolId)
                    ->first();
            } catch (\Exception $e) {
                Log::error('Error querying student: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengambil data siswa'
                ], 500);
            }

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan'
                ], 404);
            }

            // Load student choice separately - get student ID safely
            $studentChoice = null;
            $major = null;
            try {
                $studentIdValue = null;
                try {
                    $studentIdValue = $student->id ?? null;
                } catch (\Exception $e) {
                    Log::warning('Error getting student ID: ' . $e->getMessage());
                }
                
                if ($studentIdValue) {
                    $studentChoice = StudentChoice::where('student_id', $studentIdValue)->first();
                    if ($studentChoice) {
                        $majorIdValue = null;
                        try {
                            $majorIdValue = $studentChoice->major_id ?? null;
                        } catch (\Exception $e) {
                            Log::warning('Error getting major_id from choice: ' . $e->getMessage());
                        }
                        
                        if ($majorIdValue) {
                            $major = MajorRecommendation::find($majorIdValue);
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Error loading student choice or major: ' . $e->getMessage());
                // Continue without choice/major data
            }

            // Build student data with safe attribute access
            $studentData = [];
            try {
                $createdAt = null;
                $updatedAt = null;
                try {
                    if ($student->created_at) {
                        $createdAt = $student->created_at->format('c');
                    }
                } catch (\Exception $e) {
                    Log::warning('Error formatting created_at: ' . $e->getMessage());
                }
                try {
                    if ($student->updated_at) {
                        $updatedAt = $student->updated_at->format('c');
                    }
                } catch (\Exception $e) {
                    Log::warning('Error formatting updated_at: ' . $e->getMessage());
                }

                $studentData = [
                    'id' => (int)($student->id ?? 0),
                    'nisn' => (string)($student->nisn ?? ''),
                    'name' => (string)($student->name ?? ''),
                    'class' => (string)($student->kelas ?? ''),
                    'email' => (string)($student->email ?? ''),
                    'phone' => (string)($student->phone ?? ''),
                    'parent_phone' => (string)($student->parent_phone ?? ''),
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                    'has_choice' => $studentChoice ? true : false
                ];
            } catch (\Exception $e) {
                Log::error('Error building student data: ' . $e->getMessage());
                // Build minimal student data
                $studentData = [
                    'id' => (int)$studentId,
                    'nisn' => '',
                    'name' => '',
                    'class' => '',
                    'email' => '',
                    'phone' => '',
                    'parent_phone' => '',
                    'created_at' => null,
                    'updated_at' => null,
                    'has_choice' => false
                ];
            }

            // Handle chosen major if student has a choice
            if ($studentChoice && $major && is_object($major)) {
                try {
                    // Helper to parse subjects - simpler version like StudentWebController
                    $parseSubjects = function($field) {
                        if (is_null($field)) return [];
                        if (is_array($field)) {
                            // Filter and clean array values
                            return array_values(array_filter(array_map(function($item) {
                                if (is_string($item)) {
                                    return trim($item);
                                }
                                if (is_numeric($item) || is_bool($item)) {
                                    return (string)$item;
                                }
                                return null;
                            }, $field), function($item) {
                                return $item !== null && $item !== '';
                            }));
                        }
                        if (is_string($field)) {
                            $trimmed = trim($field);
                            if (empty($trimmed)) return [];
                            
                            // Try JSON decode first
                            $decoded = @json_decode($trimmed, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                return array_values(array_filter(array_map(function($item) {
                                    if (is_string($item)) {
                                        return trim($item);
                                    }
                                    if (is_numeric($item) || is_bool($item)) {
                                        return (string)$item;
                                    }
                                    return null;
                                }, $decoded), function($item) {
                                    return $item !== null && $item !== '';
                                }));
                            }
                            
                            // Fallback to comma-separated
                            $parts = explode(',', $trimmed);
                            return array_values(array_filter(array_map('trim', $parts), function($item) {
                                return !empty($item);
                            }));
                        }
                        return [];
                    };
                    
                    // Get major attributes safely with try-catch for each
                    $majorId = 0;
                    $majorName = '';
                    $majorDescription = '';
                    $majorCategory = 'Saintek';
                    $majorRumpun = 'Saintek';
                    $majorCareer = '';
                    
                    try {
                        $majorId = (int)($major->id ?? 0);
                    } catch (\Exception $e) {
                        Log::warning('Error getting major id: ' . $e->getMessage());
                    }
                    
                    try {
                        $majorName = (string)($major->major_name ?? '');
                    } catch (\Exception $e) {
                        Log::warning('Error getting major name: ' . $e->getMessage());
                    }
                    
                    try {
                        $majorDescription = (string)($major->description ?? '');
                    } catch (\Exception $e) {
                        Log::warning('Error getting major description: ' . $e->getMessage());
                    }
                    
                    try {
                        $majorCategory = (string)($major->category ?? 'Saintek');
                        $majorRumpun = (string)($major->rumpun_ilmu ?? $majorCategory);
                    } catch (\Exception $e) {
                        Log::warning('Error getting major category: ' . $e->getMessage());
                    }
                    
                    try {
                        $majorCareer = (string)($major->career_prospects ?? '');
                    } catch (\Exception $e) {
                        Log::warning('Error getting major career: ' . $e->getMessage());
                    }
                    
                    // Get subjects - handle casting from model with try-catch
                    // Use getAttribute method to safely get casted values
                    $requiredSubjects = null;
                    $preferredSubjects = null;
                    $optionalSubjects = null;
                    $kurikulumMerdeka = null;
                    $kurikulum2013Ipa = null;
                    $kurikulum2013Ips = null;
                    $kurikulum2013Bahasa = null;
                    
                    // Get subjects using toArray() to get properly casted values
                    $majorArray = null;
                    try {
                        if (method_exists($major, 'toArray')) {
                            $majorArray = $major->toArray();
                        } elseif (method_exists($major, 'getAttributes')) {
                            $majorArray = $major->getAttributes();
                        }
                    } catch (\Exception $e) {
                        Log::warning('Error converting major to array: ' . $e->getMessage());
                    }
                    
                    // Get subjects from array or directly from model
                    try {
                        $requiredSubjects = $majorArray['required_subjects'] ?? $major->required_subjects ?? null;
                    } catch (\Exception $e) {
                        Log::warning('Error getting required_subjects: ' . $e->getMessage());
                        $requiredSubjects = null;
                    }
                    
                    try {
                        $preferredSubjects = $majorArray['preferred_subjects'] ?? $major->preferred_subjects ?? null;
                    } catch (\Exception $e) {
                        Log::warning('Error getting preferred_subjects: ' . $e->getMessage());
                        $preferredSubjects = null;
                    }
                    
                    try {
                        $optionalSubjects = $majorArray['optional_subjects'] ?? $major->optional_subjects ?? null;
                    } catch (\Exception $e) {
                        Log::warning('Error getting optional_subjects: ' . $e->getMessage());
                        $optionalSubjects = null;
                    }
                    
                    try {
                        $kurikulumMerdeka = $majorArray['kurikulum_merdeka_subjects'] ?? $major->kurikulum_merdeka_subjects ?? null;
                        $kurikulum2013Ipa = $majorArray['kurikulum_2013_ipa_subjects'] ?? $major->kurikulum_2013_ipa_subjects ?? null;
                        $kurikulum2013Ips = $majorArray['kurikulum_2013_ips_subjects'] ?? $major->kurikulum_2013_ips_subjects ?? null;
                        $kurikulum2013Bahasa = $majorArray['kurikulum_2013_bahasa_subjects'] ?? $major->kurikulum_2013_bahasa_subjects ?? null;
                    } catch (\Exception $e) {
                        Log::warning('Error getting kurikulum subjects: ' . $e->getMessage());
                        $kurikulumMerdeka = null;
                        $kurikulum2013Ipa = null;
                        $kurikulum2013Ips = null;
                        $kurikulum2013Bahasa = null;
                    }
                    
                    // Format choice date safely
                    $choiceDate = null;
                    try {
                        if ($studentChoice->created_at) {
                            $choiceDate = $studentChoice->created_at->format('c');
                        }
                    } catch (\Exception $e) {
                        Log::warning('Error formatting choice_date: ' . $e->getMessage());
                    }
                    
                    // Parse all subjects safely
                    $parsedRequiredSubjects = [];
                    $parsedPreferredSubjects = [];
                    $parsedOptionalSubjects = [];
                    $parsedKurikulumMerdeka = [];
                    $parsedKurikulum2013Ipa = [];
                    $parsedKurikulum2013Ips = [];
                    $parsedKurikulum2013Bahasa = [];
                    
                    try {
                        $parsedRequiredSubjects = $parseSubjects($requiredSubjects);
                    } catch (\Exception $e) {
                        Log::warning('Error parsing required_subjects: ' . $e->getMessage());
                    }
                    
                    try {
                        $parsedPreferredSubjects = $parseSubjects($preferredSubjects);
                    } catch (\Exception $e) {
                        Log::warning('Error parsing preferred_subjects: ' . $e->getMessage());
                    }
                    
                    try {
                        $parsedOptionalSubjects = $parseSubjects($optionalSubjects);
                    } catch (\Exception $e) {
                        Log::warning('Error parsing optional_subjects: ' . $e->getMessage());
                    }
                    
                    try {
                        $parsedKurikulumMerdeka = $parseSubjects($kurikulumMerdeka);
                    } catch (\Exception $e) {
                        Log::warning('Error parsing kurikulum_merdeka_subjects: ' . $e->getMessage());
                    }
                    
                    try {
                        $parsedKurikulum2013Ipa = $parseSubjects($kurikulum2013Ipa);
                    } catch (\Exception $e) {
                        Log::warning('Error parsing kurikulum_2013_ipa_subjects: ' . $e->getMessage());
                    }
                    
                    try {
                        $parsedKurikulum2013Ips = $parseSubjects($kurikulum2013Ips);
                    } catch (\Exception $e) {
                        Log::warning('Error parsing kurikulum_2013_ips_subjects: ' . $e->getMessage());
                    }
                    
                    try {
                        $parsedKurikulum2013Bahasa = $parseSubjects($kurikulum2013Bahasa);
                    } catch (\Exception $e) {
                        Log::warning('Error parsing kurikulum_2013_bahasa_subjects: ' . $e->getMessage());
                    }
                    
                    // Build chosen_major data safely
                    try {
                        $chosenMajorData = [
                            'id' => $majorId,
                            'name' => $majorName,
                            'description' => $majorDescription,
                            'category' => $majorCategory,
                            'rumpun_ilmu' => $majorRumpun,
                            'career_prospects' => $majorCareer,
                            'education_level' => 'SMA/MA',
                            'choice_date' => $choiceDate,
                            'required_subjects' => $parsedRequiredSubjects,
                            'preferred_subjects' => $parsedPreferredSubjects,
                            'optional_subjects' => $parsedOptionalSubjects,
                            'kurikulum_merdeka_subjects' => $parsedKurikulumMerdeka,
                            'kurikulum_2013_ipa_subjects' => $parsedKurikulum2013Ipa,
                            'kurikulum_2013_ips_subjects' => $parsedKurikulum2013Ips,
                            'kurikulum_2013_bahasa_subjects' => $parsedKurikulum2013Bahasa,
                        ];
                        
                        // Validate that chosen_major can be JSON encoded
                        $testEncode = @json_encode($chosenMajorData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        if ($testEncode === false) {
                            Log::warning('chosen_major data cannot be JSON encoded, using minimal data');
                            $chosenMajorData = [
                                'id' => $majorId,
                                'name' => $majorName,
                                'description' => $majorDescription,
                                'category' => $majorCategory,
                                'rumpun_ilmu' => $majorRumpun,
                                'required_subjects' => [],
                                'preferred_subjects' => [],
                                'optional_subjects' => [],
                            ];
                        }
                        
                        $studentData['chosen_major'] = $chosenMajorData;
                    } catch (\Exception $e) {
                        Log::error('Error building chosen_major data: ' . $e->getMessage());
                        Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
                        // Continue without major data - don't fail the entire request
                    }
                } catch (\Throwable $e) {
                    Log::error('Error processing major data: ' . $e->getMessage());
                    Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
                    Log::error('Stack trace: ' . $e->getTraceAsString());
                    // Continue without major data - don't fail the entire request
                }
            }

            // Build school data safely
            $schoolData = [];
            try {
                $schoolData = [
                    'id' => (int)($school->id ?? 0),
                    'npsn' => (string)($school->npsn ?? ''),
                    'name' => (string)($school->name ?? '')
                ];
            } catch (\Exception $e) {
                Log::error('Error building school data: ' . $e->getMessage());
                $schoolData = [
                    'id' => 0,
                    'npsn' => '',
                    'name' => ''
                ];
            }

            // Return response - ensure JSON encoding works
            try {
                $responseData = [
                    'success' => true,
                    'data' => [
                        'school' => $schoolData,
                        'student' => $studentData
                    ]
                ];
                
                // Test JSON encoding before returning
                $jsonTest = json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                if ($jsonTest === false) {
                    $jsonError = json_last_error_msg();
                    Log::error('JSON encoding failed: ' . $jsonError);
                    
                    // Try without chosen_major if it exists
                    if (isset($studentData['chosen_major'])) {
                        unset($studentData['chosen_major']);
                        $responseData = [
                            'success' => true,
                            'data' => [
                                'school' => $schoolData,
                                'student' => $studentData
                            ]
                        ];
                        $jsonTest = json_encode($responseData);
                    }
                    
                    if ($jsonTest === false) {
                        throw new \Exception('JSON encoding failed: ' . json_last_error_msg());
                    }
                }
                
                return response()->json($responseData, 200);
            } catch (\Exception $e) {
                Log::error('Error encoding JSON response: ' . $e->getMessage());
                // Return minimal response
                try {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'school' => ['id' => 0, 'npsn' => '', 'name' => ''],
                            'student' => ['id' => (int)$studentId, 'name' => '', 'has_choice' => false]
                        ]
                    ], 200);
                } catch (\Exception $e2) {
                    // Last resort - return plain text
                    http_response_code(200);
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Error processing response']);
                    exit;
                }
            }

        } catch (\Throwable $e) {
            // Log langsung ke PHP error log (bypass Laravel log system)
            error_log('=== studentDetail FATAL ERROR ===');
            error_log('Error: ' . $e->getMessage());
            error_log('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            // Log semua jenis error termasuk Error dan Exception
            try {
                Log::error('Get student detail error: ' . $e->getMessage());
                Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
                Log::error('Stack trace: ' . $e->getTraceAsString());
            } catch (\Throwable $logError) {
                error_log('Laravel Log failed: ' . $logError->getMessage());
            }
            
            // Return error response
            try {
                $errorMessage = config('app.debug') ? $e->getMessage() : null;
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan server. Silakan coba lagi.',
                    'error' => $errorMessage,
                    'error_type' => get_class($e)
                ], 500);
            } catch (\Throwable $responseError) {
                // Jika response juga gagal, return minimal
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Terjadi kesalahan server'
                ]);
                exit;
            }
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
                    'rumpun_ilmu' => 'Saintek',
                    'career_prospects' => '',
                    'education_level' => 'SMA/MA',
                    'choice_date' => null,
                    'required_subjects' => [],
                    'preferred_subjects' => [],
                    'optional_subjects' => [],
                    'kurikulum_merdeka_subjects' => [],
                    'kurikulum_2013_ipa_subjects' => [],
                    'kurikulum_2013_ips_subjects' => [],
                    'kurikulum_2013_bahasa_subjects' => []
                ];
            }
            
            $majorId = $this->safeGetAttribute($major, 'id');
            $majorName = $this->safeGetAttribute($major, 'major_name');
            $majorDescription = $this->safeGetAttribute($major, 'description');
            $majorCategory = $this->safeGetAttribute($major, 'category');
            $majorRumpun = $this->safeGetAttribute($major, 'rumpun_ilmu');
            $majorCareer = $this->safeGetAttribute($major, 'career_prospects');
            $choiceDate = null;
            
            try {
                if ($studentChoice) {
                    $choiceDate = $this->safeGetAttribute($studentChoice, 'created_at');
                }
            } catch (\Throwable $e) {
                // Ignore error getting choice date
            }
            
            return [
                'id' => is_numeric($majorId) ? (int)$majorId : 0,
                'name' => is_string($majorName) ? $majorName : (string)($majorName ?? 'Unknown'),
                'description' => is_string($majorDescription) ? $majorDescription : (string)($majorDescription ?? ''),
                'category' => is_string($majorCategory) ? $majorCategory : 'Saintek',
                'rumpun_ilmu' => is_string($majorRumpun) ? $majorRumpun : (is_string($majorCategory) ? $majorCategory : 'Saintek'),
                'career_prospects' => is_string($majorCareer) ? $majorCareer : (string)($majorCareer ?? ''),
                'education_level' => 'SMA/MA',
                'choice_date' => $choiceDate,
                'required_subjects' => [],
                'preferred_subjects' => [],
                'optional_subjects' => [],
                'kurikulum_merdeka_subjects' => [],
                'kurikulum_2013_ipa_subjects' => [],
                'kurikulum_2013_ips_subjects' => [],
                'kurikulum_2013_bahasa_subjects' => []
            ];
        } catch (\Throwable $e) {
            Log::warning('Error in createBasicMajorData: ' . $e->getMessage());
            return [
                'id' => 0,
                'name' => 'Unknown',
                'description' => '',
                'category' => 'Saintek',
                'rumpun_ilmu' => 'Saintek',
                'career_prospects' => '',
                'education_level' => 'SMA/MA',
                'choice_date' => null,
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

    /**
     * Get major with subjects from database mapping
     * Uses same approach as StudentWebController::formatMajorWithSubjects
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
            
            // Helper function to parse subjects from JSON or string (simpler version like StudentWebController)
            $parseSubjects = function($field) {
                try {
                    if (is_null($field)) return [];
                    if (is_array($field)) return array_values($field);
                    if (is_string($field)) {
                        if (strlen($field) === 0) return [];
                        // Try to decode as JSON first
                        $decoded = @json_decode($field, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            return array_values($decoded);
                        }
                        // If not JSON, treat as comma-separated string
                        $parts = explode(',', $field);
                        $filtered = array_filter(array_map('trim', $parts), function($item) {
                            return !empty($item);
                        });
                        return array_values($filtered);
                    }
                    return [];
                } catch (\Throwable $e) {
                    Log::warning('Error in parseSubjects: ' . $e->getMessage());
                    return [];
                }
            };
            
            // Get subjects - use same approach as StudentWebController::formatMajorWithSubjects
            $mandatorySubjects = [];
            $optionalSubjects = [];
            
            // First, try to get from database mapping (like StudentWebController does)
            try {
                if (isset($major->id) && is_numeric($major->id) && class_exists('\App\Models\MajorSubjectMapping')) {
                    try {
                        $mappings = \App\Models\MajorSubjectMapping::where('major_id', $major->id)
                            ->where('is_active', 1)
                            ->get();
                        
                        if ($mappings && $mappings->count() > 0) {
                            // Load subjects separately to avoid relationship issues (safer approach)
                            foreach ($mappings as $mapping) {
                                try {
                                    if (isset($mapping->subject_id) && is_numeric($mapping->subject_id)) {
                                        $subject = \App\Models\Subject::find($mapping->subject_id);
                                        if ($subject && isset($subject->name) && !empty($subject->name)) {
                                            $subjectName = trim($subject->name);
                                            if ($mapping->mapping_type === 'wajib') {
                                                if (!in_array($subjectName, $mandatorySubjects)) {
                                                    $mandatorySubjects[] = $subjectName;
                                                }
                                            } elseif ($mapping->mapping_type === 'pilihan') {
                                                if (!in_array($subjectName, $optionalSubjects)) {
                                                    $optionalSubjects[] = $subjectName;
                                                }
                                            }
                                        }
                                    }
                                } catch (\Exception $subjectError) {
                                    // Skip this subject and continue
                                    continue;
                                }
                            }
                        }
                    } catch (\Exception $mappingError) {
                        Log::warning('Failed to load subject mappings: ' . $mappingError->getMessage(), [
                            'major_id' => $major->id ?? 'unknown',
                            'file' => $mappingError->getFile(),
                            'line' => $mappingError->getLine()
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Error attempting subject mapping: ' . $e->getMessage(), [
                    'major_id' => $major->id ?? 'unknown',
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
            
            // If no mappings found, try to get from required_subjects and preferred_subjects fields (like StudentWebController)
            if (empty($mandatorySubjects)) {
                try {
                    $requiredField = $this->safeGetAttribute($major, 'required_subjects');
                    if (!empty($requiredField)) {
                        $mandatorySubjects = $parseSubjects($requiredField);
                    }
                } catch (\Exception $e) {
                    Log::warning('Error parsing required_subjects: ' . $e->getMessage());
                }
            }
            
            if (empty($optionalSubjects)) {
                try {
                    $preferredField = $this->safeGetAttribute($major, 'preferred_subjects');
                    if (!empty($preferredField)) {
                        $optionalSubjects = $parseSubjects($preferredField);
                    }
                } catch (\Exception $e) {
                    Log::warning('Error parsing preferred_subjects: ' . $e->getMessage());
                }
            }
            
            // If still empty, try optional_subjects field
            if (empty($optionalSubjects)) {
                try {
                    $optionalField = $this->safeGetAttribute($major, 'optional_subjects');
                    if (!empty($optionalField)) {
                        $optionalSubjects = $parseSubjects($optionalField);
                    }
                } catch (\Exception $e) {
                    Log::warning('Error parsing optional_subjects: ' . $e->getMessage());
                }
            }
        
            // Safely parse kurikulum subjects with error handling
            $kurikulumMerdekaSubjects = [];
            $kurikulum2013IpaSubjects = [];
            $kurikulum2013IpsSubjects = [];
            $kurikulum2013BahasaSubjects = [];
            
            try {
                $kurikulumMerdekaField = $this->safeGetAttribute($major, 'kurikulum_merdeka_subjects');
                $kurikulumMerdekaSubjects = $parseSubjects($kurikulumMerdekaField);
            } catch (\Exception $e) {
                Log::warning('Error parsing kurikulum_merdeka_subjects: ' . $e->getMessage());
            }
            
            try {
                $kurikulum2013IpaField = $this->safeGetAttribute($major, 'kurikulum_2013_ipa_subjects');
                $kurikulum2013IpaSubjects = $parseSubjects($kurikulum2013IpaField);
            } catch (\Exception $e) {
                Log::warning('Error parsing kurikulum_2013_ipa_subjects: ' . $e->getMessage());
            }
            
            try {
                $kurikulum2013IpsField = $this->safeGetAttribute($major, 'kurikulum_2013_ips_subjects');
                $kurikulum2013IpsSubjects = $parseSubjects($kurikulum2013IpsField);
            } catch (\Exception $e) {
                Log::warning('Error parsing kurikulum_2013_ips_subjects: ' . $e->getMessage());
            }
            
            try {
                $kurikulum2013BahasaField = $this->safeGetAttribute($major, 'kurikulum_2013_bahasa_subjects');
                $kurikulum2013BahasaSubjects = $parseSubjects($kurikulum2013BahasaField);
            } catch (\Exception $e) {
                Log::warning('Error parsing kurikulum_2013_bahasa_subjects: ' . $e->getMessage());
            }
            
            // Ensure all values are JSON-serializable (convert objects to arrays, handle nulls)
            $majorId = $this->safeGetAttribute($major, 'id');
            $majorName = $this->safeGetAttribute($major, 'major_name');
            $majorDescription = $this->safeGetAttribute($major, 'description');
            $majorCareer = $this->safeGetAttribute($major, 'career_prospects');
            $majorCategory = $this->safeGetAttribute($major, 'category');
            $majorRumpun = $this->safeGetAttribute($major, 'rumpun_ilmu');
            
            $majorData = [
                'id' => is_numeric($majorId) ? (int)$majorId : 0,
                'name' => is_string($majorName) ? $majorName : (string)($majorName ?? 'Unknown Major'),
                'description' => is_string($majorDescription) ? $majorDescription : (string)($majorDescription ?? ''),
                'career_prospects' => is_string($majorCareer) ? $majorCareer : (string)($majorCareer ?? ''),
                'category' => is_string($majorCategory) ? $majorCategory : 'Saintek',
                'rumpun_ilmu' => is_string($majorRumpun) ? $majorRumpun : (is_string($majorCategory) ? $majorCategory : 'Saintek'),
                'education_level' => is_string($educationLevel) ? $educationLevel : 'SMA/MA',
                'required_subjects' => is_array($mandatorySubjects) ? array_values($mandatorySubjects) : [],
                'preferred_subjects' => is_array($optionalSubjects) ? array_values($optionalSubjects) : [],
                'optional_subjects' => is_array($optionalSubjects) ? array_values($optionalSubjects) : [],
                'kurikulum_merdeka_subjects' => is_array($kurikulumMerdekaSubjects) ? array_values($kurikulumMerdekaSubjects) : [],
                'kurikulum_2013_ipa_subjects' => is_array($kurikulum2013IpaSubjects) ? array_values($kurikulum2013IpaSubjects) : [],
                'kurikulum_2013_ips_subjects' => is_array($kurikulum2013IpsSubjects) ? array_values($kurikulum2013IpsSubjects) : [],
                'kurikulum_2013_bahasa_subjects' => is_array($kurikulum2013BahasaSubjects) ? array_values($kurikulum2013BahasaSubjects) : []
            ];
            
            // Log for debugging
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
            // Get school from middleware first
            $school = $request->school;
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah tidak ditemukan'
                ], 404);
            }

            // Log request data for debugging
            Log::info('Import students request received', [
                'school_id' => $school->id ?? 'unknown',
                'file_name' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'No file',
                'file_size' => $request->file('file') ? $request->file('file')->getSize() : 0
            ]);
            
            // Custom validation untuk file
            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 400);
            }

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
