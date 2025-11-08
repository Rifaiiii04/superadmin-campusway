<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\SchoolAuthController;
use App\Http\Controllers\StudentWebController;
use App\Http\Controllers\SchoolDashboardController;
use App\Http\Controllers\TkaScheduleController;

// ===========================================
// STUDENT WEB API ROUTES (Public - No Auth)
// ===========================================
Route::prefix('web')->group(function () {
    // Public endpoints
    Route::get('/schools', [StudentWebController::class, 'getSchools']);
    Route::get('/questions', [ApiController::class, 'getQuestions']);
    Route::get('/majors', [StudentWebController::class, 'getMajors']);
    Route::get('/majors/{id}', [StudentWebController::class, 'getMajorDetails']);
    Route::get('/health', [ApiController::class, 'healthCheck']);
    
    // Student authentication
    Route::post('/register-student', [StudentWebController::class, 'register']);
    Route::post('/login', [StudentWebController::class, 'login']);
    
    // Student major selection (no auth required for simplicity)
    Route::post('/choose-major', [StudentWebController::class, 'chooseMajor']);
    Route::post('/change-major', [StudentWebController::class, 'changeMajor']);
    Route::get('/student-choice/{studentId}', [StudentWebController::class, 'getStudentChoice']);
    Route::get('/major-status/{studentId}', [StudentWebController::class, 'checkMajorStatus']);
    Route::get('/student-profile/{studentId}', [StudentWebController::class, 'getStudentProfile']);
    
    // TKA Schedules for students
    Route::get('/tka-schedules', [TkaScheduleController::class, 'index']);
    Route::get('/tka-schedules/upcoming', [TkaScheduleController::class, 'upcoming']);
});

// ===========================================
// SCHOOL DASHBOARD API ROUTES (With Auth)
// ===========================================
Route::prefix('school')->group(function () {
    // School authentication
    Route::post('/login', [SchoolAuthController::class, 'login']);
    Route::post('/logout', [SchoolAuthController::class, 'logout'])->middleware('school.auth');
    
    // TEST ENDPOINT - No auth required for debugging
    Route::get('/test-student-detail/{id}', function(Request $request, $id) {
        \Illuminate\Support\Facades\Log::info('TEST endpoint called', ['id' => $id]);
        return response()->json([
            'success' => true,
            'message' => 'Test endpoint works',
            'id' => $id,
            'request_uri' => $request->getRequestUri(),
            'method' => $request->method()
        ]);
    });
    
    // Protected routes (require authentication)
    Route::middleware('school.auth')->group(function () {
        Route::get('/profile', [SchoolAuthController::class, 'profile']);
        Route::get('/dashboard', [SchoolDashboardController::class, 'index']);
        Route::get('/students', [SchoolDashboardController::class, 'students']);
        Route::get('/students/{id}', [SchoolDashboardController::class, 'studentDetail']);
        Route::get('/major-statistics', [SchoolDashboardController::class, 'majorStatistics']);
        Route::get('/export-students', [SchoolDashboardController::class, 'exportStudents']);
        Route::get('/students-without-choice', [SchoolDashboardController::class, 'studentsWithoutChoice']);
        
        // Student management
        Route::post('/students', [SchoolDashboardController::class, 'addStudent']);
        Route::put('/students/{id}', [SchoolDashboardController::class, 'updateStudent']);
        Route::delete('/students/{id}', [SchoolDashboardController::class, 'deleteStudent']);
        
        // Import students
        Route::post('/import-students', [SchoolDashboardController::class, 'importStudents']);
        Route::get('/import-template', [SchoolDashboardController::class, 'downloadTemplate']);
        Route::get('/import-rules', [SchoolDashboardController::class, 'importRules']);
        
        // Classes
        Route::get('/classes', [SchoolDashboardController::class, 'getClasses']);
        
        // TKA Schedules
        Route::get('/tka-schedules', [TkaScheduleController::class, 'index']);
        Route::get('/tka-schedules/upcoming', [TkaScheduleController::class, 'upcoming']);
    });
});

// ===========================================
// PUBLIC API ROUTES (SuperAdmin Integration)
// ===========================================
Route::prefix('public')->group(function () {
    Route::get('/schools', [ApiController::class, 'getSchools']);
    Route::get('/questions', [ApiController::class, 'getQuestions']);
    Route::get('/majors', [ApiController::class, 'getMajors']);
    Route::get('/health', [ApiController::class, 'healthCheck']);
    
    // Student web API routes
    Route::post('/login', [StudentWebController::class, 'login']);
    Route::post('/register-student', [StudentWebController::class, 'registerStudent']);
    Route::get('/schools', [StudentWebController::class, 'getSchools']);
    Route::get('/majors', [StudentWebController::class, 'getMajors']);
    Route::get('/majors/{id}', [StudentWebController::class, 'getMajorDetails']);
    Route::get('/major-status/{studentId}', [StudentWebController::class, 'checkMajorStatus']);
    Route::get('/student-choice/{studentId}', [StudentWebController::class, 'getStudentChoice']);
    Route::post('/choose-major', [StudentWebController::class, 'chooseMajor']);
    Route::post('/change-major', [StudentWebController::class, 'changeMajor']);
    Route::get('/student-profile/{studentId}', [StudentWebController::class, 'getStudentProfile']);
    Route::get('/student-subjects/{studentId}', [StudentWebController::class, 'getStudentSubjects']);
    Route::get('/subjects-for-major', [StudentWebController::class, 'getSubjectsForMajor']);
    Route::get('/tka-schedules', [StudentWebController::class, 'getTkaSchedules']);
    Route::get('/tka-schedules/upcoming', [StudentWebController::class, 'getUpcomingTkaSchedules']);
});

// School authentication routes (no middleware required for login)
Route::prefix('school')->group(function () {
    Route::post('/login', [SchoolAuthController::class, 'login']);
    Route::post('/logout', [SchoolAuthController::class, 'logout']);
    Route::get('/profile', [SchoolAuthController::class, 'profile']);
    
    // Test route without authentication
    Route::get('/test-dashboard', function () {
        return response()->json([
            'success' => true,
            'message' => 'CORS test successful',
            'data' => [
                'cors_working' => true,
                'timestamp' => now()
            ]
        ]);
    });
    
    // New test dashboard endpoint with complete data
    Route::get('/dashboard-full', function () {
        try {
            // Get first school for testing
            $school = \App\Models\School::first();
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data sekolah di database'
                ]);
            }

            // Get real student data
            $totalStudents = \App\Models\Student::where('school_id', $school->id)->count();
            $studentsWithChoice = \App\Models\StudentChoice::whereHas('student', function($query) use ($school) {
                $query->where('school_id', $school->id);
            })->count();
            $studentsWithoutChoice = $totalStudents - $studentsWithChoice;

            // Get students by class
            $studentsByClass = \App\Models\Student::where('school_id', $school->id)
                ->selectRaw('kelas, COUNT(*) as student_count')
                ->groupBy('kelas')
                ->orderBy('kelas')
                ->get();

            // Get top majors
            $topMajors = \App\Models\StudentChoice::whereHas('student', function($query) use ($school) {
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

            // If no real data, return sample data for testing
            if ($totalStudents == 0) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'school' => [
                            'id' => $school->id,
                            'name' => $school->name,
                            'npsn' => $school->npsn,
                        ],
                        'statistics' => [
                            'total_students' => 150,
                            'students_with_choice' => 75,
                            'students_without_choice' => 75,
                            'completion_percentage' => 50.0
                        ],
                        'top_majors' => [
                            [
                                'major_id' => '1',
                                'major_name' => 'Teknik Informatika',
                                'category' => 'Saintek',
                                'student_count' => 25
                            ],
                            [
                                'major_id' => '2',
                                'major_name' => 'Teknik Mesin',
                                'category' => 'Saintek',
                                'student_count' => 20
                            ],
                            [
                                'major_id' => '3',
                                'major_name' => 'Akuntansi',
                                'category' => 'Soshum',
                                'student_count' => 15
                            ],
                            [
                                'major_id' => '4',
                                'major_name' => 'Teknik Elektro',
                                'category' => 'Saintek',
                                'student_count' => 10
                            ],
                            [
                                'major_id' => '5',
                                'major_name' => 'Manajemen',
                                'category' => 'Soshum',
                                'student_count' => 5
                            ]
                        ],
                        'students_by_class' => [
                            [
                                'kelas' => 'X IPA 1',
                                'student_count' => 30
                            ],
                            [
                                'kelas' => 'X IPA 2',
                                'student_count' => 28
                            ],
                            [
                                'kelas' => 'X IPS 1',
                                'student_count' => 32
                            ],
                            [
                                'kelas' => 'XI IPA 1',
                                'student_count' => 25
                            ],
                            [
                                'kelas' => 'XI IPA 2',
                                'student_count' => 27
                            ],
                            [
                                'kelas' => 'XI IPS 1',
                                'student_count' => 28
                            ]
                        ]
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'name' => $school->name,
                        'npsn' => $school->npsn,
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
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    });
    
    // Test dashboard without authentication - with complete data
    Route::get('/test-dashboard-data', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'school' => [
                    'id' => 8,
                    'name' => 'SMK Negeri 1 Karawang',
                    'npsn' => '44556677',
                ],
                'statistics' => [
                    'total_students' => 150,
                    'students_with_choice' => 75,
                    'students_without_choice' => 75,
                    'completion_percentage' => 50.0
                ],
                'top_majors' => [
                    [
                        'major_id' => '1',
                        'major_name' => 'Teknik Informatika',
                        'category' => 'Saintek',
                        'student_count' => 25
                    ],
                    [
                        'major_id' => '2',
                        'major_name' => 'Teknik Mesin',
                        'category' => 'Saintek',
                        'student_count' => 20
                    ],
                    [
                        'major_id' => '3',
                        'major_name' => 'Akuntansi',
                        'category' => 'Soshum',
                        'student_count' => 15
                    ],
                    [
                        'major_id' => '4',
                        'major_name' => 'Teknik Elektro',
                        'category' => 'Saintek',
                        'student_count' => 10
                    ],
                    [
                        'major_id' => '5',
                        'major_name' => 'Manajemen',
                        'category' => 'Soshum',
                        'student_count' => 5
                    ]
                ],
                'students_by_class' => [
                    [
                        'kelas' => 'X IPA 1',
                        'student_count' => 30
                    ],
                    [
                        'kelas' => 'X IPA 2',
                        'student_count' => 28
                    ],
                    [
                        'kelas' => 'X IPS 1',
                        'student_count' => 32
                    ],
                    [
                        'kelas' => 'XI IPA 1',
                        'student_count' => 25
                    ],
                    [
                        'kelas' => 'XI IPA 2',
                        'student_count' => 27
                    ],
                    [
                        'kelas' => 'XI IPS 1',
                        'student_count' => 28
                    ]
                ]
            ]
        ]);
    });

    // Test students endpoint without authentication
    Route::get('/test-students', function () {
        try {
            $school = \App\Models\School::first();
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data sekolah di database'
                ]);
            }

            $students = \App\Models\Student::where('school_id', $school->id)
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
                            'id' => $student->studentChoice->major_id,
                            'name' => $student->studentChoice->major->major_name ?? 'Jurusan Tidak Ditemukan',
                            'category' => $student->studentChoice->major->category ?? 'Saintek',
                            'rumpun_ilmu' => $student->studentChoice->major->rumpun_ilmu ?? $student->studentChoice->major->category ?? 'ILMU TERAPAN'
                        ] : null,
                        'choice_date' => $student->studentChoice ? $student->studentChoice->created_at : null
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'name' => $school->name,
                        'npsn' => $school->npsn,
                    ],
                    'students' => $students,
                    'total_students' => $students->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    });

    // Test major statistics endpoint without authentication
    Route::get('/test-major-statistics', function () {
        try {
            $school = \App\Models\School::first();
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data sekolah di database'
                ]);
            }

            $majorStats = \App\Models\StudentChoice::whereHas('student', function($query) use ($school) {
                $query->where('school_id', $school->id);
            })
            ->with('major')
            ->selectRaw('major_id, COUNT(*) as student_count')
            ->groupBy('major_id')
            ->orderBy('student_count', 'desc')
            ->get()
            ->map(function($choice) use ($school) {
                $total = \App\Models\StudentChoice::whereHas('student', function($query) use ($school) {
                    $query->where('school_id', $school->id);
                })->count();
                
                return [
                    'major_id' => $choice->major_id,
                    'major_name' => $choice->major->major_name ?? 'Jurusan Tidak Ditemukan',
                    'category' => $choice->major->category ?? 'Saintek',
                    'student_count' => $choice->student_count,
                    'percentage' => $total > 0 ? round(($choice->student_count / $total) * 100, 2) : 0
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => [
                        'id' => $school->id,
                        'name' => $school->name,
                        'npsn' => $school->npsn,
                    ],
                    'major_statistics' => $majorStats
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    });

    // Debug endpoint to check database data
    Route::get('/debug-data', function () {
        try {
            $schools = \App\Models\School::count();
            $students = \App\Models\Student::count();
            $studentChoices = \App\Models\StudentChoice::count();
            $majors = \App\Models\MajorRecommendation::count();
            
            $firstSchool = \App\Models\School::first();
            $studentsInFirstSchool = $firstSchool ? \App\Models\Student::where('school_id', $firstSchool->id)->count() : 0;
            
            return response()->json([
                'success' => true,
                'debug' => [
                    'total_schools' => $schools,
                    'total_students' => $students,
                    'total_student_choices' => $studentChoices,
                    'total_majors' => $majors,
                    'first_school' => $firstSchool ? [
                        'id' => $firstSchool->id,
                        'name' => $firstSchool->name,
                        'npsn' => $firstSchool->npsn
                    ] : null,
                    'students_in_first_school' => $studentsInFirstSchool
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    });
    
    // Protected school routes (with authentication) - REMOVED DUPLICATE ROUTES
    // These routes are already defined above with proper school.auth middleware
});

// ===========================================
// EXPORT ROUTES (No middleware)
// ===========================================
Route::get('/major-recommendations/export', [App\Http\Controllers\MajorRecommendationController::class, 'export']);

// TKA Schedules routes (public access)
Route::get('/tka-schedules', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'apiIndex']);
Route::get('/tka-schedules/upcoming', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'upcoming']);

// TKA Schedules CRUD operations (for teacher dashboard)
Route::post('/tka-schedules', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'store']);
Route::put('/tka-schedules/{id}', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'update']);
Route::delete('/tka-schedules/{id}', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'destroy']);
