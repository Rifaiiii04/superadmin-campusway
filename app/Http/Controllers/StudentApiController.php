<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\School;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\TestResult;
use App\Models\TestAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Subject; // Added missing import

class StudentApiController extends Controller
{
    /**
     * Input data identitas siswa
     */
    public function registerStudent(Request $request)
    {
        try {
            // Debug: log semua data yang diterima
            Log::info('Student registration request received', [
                'all_data' => $request->all(),
                'query_params' => $request->query(),
                'json_body' => $request->json(),
                'content_type' => $request->header('Content-Type'),
                'accept' => $request->header('Accept')
            ]);

            $validator = Validator::make($request->all(), [
                'nama_lengkap' => 'required|string|max:255',
                'nisn' => 'required|string|unique:students,nisn|max:20',
                'npsn_sekolah' => 'required|string|max:20',
                'nama_sekolah' => 'required|string|max:255',
                'kelas' => 'required|string|max:10',
                'no_handphone' => 'required|string|max:15',
                'email' => 'required|email|max:255',
                'no_orang_tua' => 'required|string|max:15',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validasi NPSN dan nama sekolah
            $school = School::where('npsn', $request->npsn_sekolah)->first();
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'NPSN sekolah tidak ditemukan',
                    'debug' => [
                        'requested_npsn' => $request->npsn_sekolah,
                        'available_npsn' => School::pluck('npsn')->toArray()
                    ]
                ], 404);
            }

            // Debug: log untuk troubleshooting
            Log::info('School validation debug', [
                'request_nama_sekolah' => $request->nama_sekolah,
                'db_school_name' => $school->name,
                'request_npsn' => $request->npsn_sekolah,
                'db_school_npsn' => $school->npsn,
                'comparison' => [
                    'exact_match' => $request->nama_sekolah === $school->name,
                    'trimmed_match' => trim($request->nama_sekolah) === trim($school->name),
                    'lowercase_match' => strtolower(trim($request->nama_sekolah)) === strtolower(trim($school->name)),
                    'length_request' => strlen($request->nama_sekolah),
                    'length_db' => strlen($school->name)
                ]
            ]);

            if (strtolower(trim($school->name)) !== strtolower(trim($request->nama_sekolah))) {
                return response()->json([
                    'success' => false,
                    'message' => 'NPSN tidak cocok dengan nama sekolah',
                    'debug' => [
                        'request_nama_sekolah' => $request->nama_sekolah,
                        'db_school_name' => $school->name,
                        'request_npsn' => $request->npsn_sekolah,
                        'db_school_npsn' => $school->npsn,
                        'comparison' => [
                            'exact_match' => $request->nama_sekolah === $school->name,
                            'trimmed_match' => trim($request->nama_sekolah) === trim($school->name),
                            'lowercase_match' => strtolower(trim($request->nama_sekolah)) === strtolower(trim($school->name)),
                            'length_request' => strlen($request->nama_sekolah),
                            'length_db' => strlen($school->name)
                        ]
                    ]
                ], 422);
            }

            // Buat siswa baru dengan mapping kolom yang benar
            $student = Student::create([
                'name' => $request->nama_lengkap,           // nama_lengkap → name
                'nisn' => $request->nisn,                  // nisn → nisn
                'school_id' => $school->id,                // npsn_sekolah → school_id (foreign key)
                'kelas' => $request->kelas,                // kelas → kelas
                'phone' => $request->no_handphone,         // no_handphone → phone
                'email' => $request->email,                // email → email
                'status' => 'registered'                   // status default
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registrasi siswa berhasil',
                'data' => [
                    'student_id' => $student->id,
                    'nama_lengkap' => $student->name,
                    'nisn' => $student->nisn,
                    'nama_sekolah' => $school->name,
                    'school_id' => $student->school_id
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error registering student: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Ambil soal berdasarkan mata pelajaran yang dipilih
     */
    public function getQuestions(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'student_id' => 'required|exists:students,id',
                'subjects' => 'required|array|min:5|max:5',
                'subjects.*' => 'required|exists:subjects,name'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->fails()
                ], 422);
            }

            $student = Student::findOrFail($request->student_id);
            
            // Cek apakah siswa sudah pernah tes
            $existingTest = TestResult::where('student_id', $student->id)->first();
            if ($existingTest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa sudah pernah melakukan tes'
                ], 400);
            }

            $questions = [];
            $subjects = $request->subjects;

            foreach ($subjects as $subject) {
                $subjectQuestions = Question::where('subject', $subject)
                    ->where('type', 'multiple_choice')
                    ->with(['options' => function($query) {
                        $query->select('id', 'question_id', 'option_text', 'is_correct');
                    }])
                    ->inRandomOrder()
                    ->limit(20) // 20 soal per mata pelajaran
                    ->get();

                $questions[$subject] = $subjectQuestions->map(function($question) {
                    return [
                        'id' => $question->id,
                        'question_text' => $question->question_text,
                        'media_url' => $question->media_url,
                        'options' => $question->options->map(function($option) {
                            return [
                                'id' => $option->id,
                                'option_text' => $option->option_text
                            ];
                        })
                    ];
                });
            }

            // Buat test session
            $testResult = TestResult::create([
                'student_id' => $student->id,
                'subjects' => json_encode($subjects),
                'start_time' => now(),
                'status' => 'ongoing'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Soal berhasil diambil',
                'data' => [
                    'test_id' => $testResult->id,
                    'questions' => $questions,
                    'start_time' => $testResult->start_time,
                    'time_limit' => 120 // 2 jam dalam menit
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting questions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Submit jawaban siswa
     */
    public function submitAnswers(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'test_id' => 'required|exists:test_results,id',
                'answers' => 'required|array',
                'answers.*.question_id' => 'required|exists:questions,id',
                'answers.*.selected_option_id' => 'required|exists:question_options,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $testResult = TestResult::findOrFail($request->test_id);
            
            if ($testResult->status !== 'ongoing') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tes sudah selesai atau tidak valid'
                ], 400);
            }

            // Simpan jawaban
            foreach ($request->answers as $answer) {
                TestAnswer::updateOrCreate(
                    [
                        'test_result_id' => $testResult->id,
                        'question_id' => $answer['question_id']
                    ],
                    [
                        'selected_option_id' => $answer['selected_option_id'],
                        'answered_at' => now()
                    ]
                );
            }

            // Hitung skor
            $scores = $this->calculateScores($testResult->id);
            
            // Update test result
            $testResult->update([
                'end_time' => now(),
                'status' => 'completed',
                'scores' => json_encode($scores),
                'total_score' => array_sum(array_column($scores, 'score')),
                'recommendations' => $this->generateRecommendations($scores)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil disimpan',
                'data' => [
                    'test_id' => $testResult->id,
                    'scores' => $scores,
                    'total_score' => $testResult->total_score,
                    'recommendations' => $testResult->recommendations
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error submitting answers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Lihat hasil dan rekomendasi
     */
    public function getResults($testId)
    {
        try {
            $testResult = TestResult::with(['student.school'])
                ->where('id', $testId)
                ->where('status', 'completed')
                ->first();

            if (!$testResult) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hasil tes tidak ditemukan atau belum selesai',
                    'debug' => [
                        'test_id' => $testId,
                        'available_tests' => TestResult::select('id', 'status')->get()
                    ]
                ], 404);
            }

            // Pastikan student dan school ada
            if (!$testResult->student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data siswa tidak ditemukan'
                ], 404);
            }

            $scores = json_decode($testResult->scores, true) ?? [];
            $recommendations = json_decode($testResult->recommendations, true) ?? [];

            return response()->json([
                'success' => true,
                'message' => 'Hasil tes berhasil diambil',
                'data' => [
                    'student' => [
                        'nama_lengkap' => $testResult->student->name,
                        'nisn' => $testResult->student->nisn,
                        'nama_sekolah' => $testResult->student->school ? $testResult->student->school->name : 'N/A',
                        'kelas' => $testResult->student->kelas
                    ],
                    'test_info' => [
                        'test_id' => $testResult->id,
                        'start_time' => $testResult->start_time,
                        'end_time' => $testResult->end_time,
                        'duration' => $testResult->start_time->diffInMinutes($testResult->end_time),
                        'status' => $testResult->status
                    ],
                    'scores' => $scores,
                    'total_score' => $testResult->total_score ?? 0,
                    'average_score' => !empty($scores) ? round(($testResult->total_score ?? 0) / count($scores), 2) : 0,
                    'recommendations' => $recommendations
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting results: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export hasil PDF
     */
    public function exportPdf($testId)
    {
        try {
            $testResult = TestResult::with('student')
                ->where('id', $testId)
                ->where('status', 'completed')
                ->first();

            if (!$testResult) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hasil tes tidak ditemukan'
                ], 404);
            }

            $scores = json_decode($testResult->scores, true);
            $recommendations = json_decode($testResult->recommendations, true);

            $data = [
                'student' => $testResult->student,
                'test_info' => [
                    'start_time' => $testResult->start_time,
                    'end_time' => $testResult->end_time,
                    'duration' => $testResult->start_time->diffInMinutes($testResult->end_time)
                ],
                'scores' => $scores,
                'total_score' => $testResult->total_score,
                'average_score' => round($testResult->total_score / count($scores), 2),
                'recommendations' => $recommendations
            ];

            $pdf = PDF::loadView('pdf.test-result', $data);
            
            $filename = 'hasil_tes_' . $testResult->student->nisn . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Auto-save jawaban (untuk timer dan auto-save)
     */
    public function autoSaveAnswer(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'test_id' => 'required|exists:test_results,id',
                'question_id' => 'required|exists:questions,id',
                'selected_option_id' => 'required|exists:question_options,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $testResult = TestResult::findOrFail($request->test_id);
            
            if ($testResult->status !== 'ongoing') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tes sudah selesai'
                ], 400);
            }

            TestAnswer::updateOrCreate(
                [
                    'test_result_id' => $testResult->id,
                    'question_id' => $request->question_id
                ],
                [
                    'selected_option_id' => $request->selected_option_id,
                    'answered_at' => now()
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error auto-saving answer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Test endpoint untuk debugging - GET method
     */
    public function testEndpoint()
    {
        return response()->json([
            'success' => true,
            'message' => 'Student API is working!',
            'timestamp' => now(),
            'endpoints' => [
                'POST /api/student/register' => 'Registrasi siswa',
                'POST /api/student/questions' => 'Ambil soal tes',
                'POST /api/student/submit-answers' => 'Submit jawaban',
                'POST /api/student/auto-save' => 'Auto-save jawaban',
                'GET /api/student/results/{testId}' => 'Lihat hasil tes',
                'GET /api/student/export-pdf/{testId}' => 'Download PDF',
                'GET /api/student/test' => 'Test endpoint (ini)'
            ]
        ]);
    }

    /**
     * Simple test endpoint untuk debugging submit-answers
     */
    public function testSubmitAnswers()
    {
        try {
            // Test basic functionality
            $testData = [
                'test_id' => 1,
                'answers' => [
                    [
                        'question_id' => 1,
                        'selected_option_id' => 1
                    ]
                ]
            ];

            // Test validation
            $validator = Validator::make($testData, [
                'test_id' => 'required|exists:test_results,id',
                'answers' => 'required|array',
                'answers.*.question_id' => 'required|exists:questions,id',
                'answers.*.selected_option_id' => 'required|exists:question_options,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Test database connections
            $testResult = TestResult::find(1);
            $question = Question::find(1);
            $option = QuestionOption::find(1);

            return response()->json([
                'success' => true,
                'message' => 'Test submit answers completed',
                'data' => [
                    'validation_passed' => true,
                    'test_result_exists' => $testResult ? true : false,
                    'question_exists' => $question ? true : false,
                    'option_exists' => $option ? true : false,
                    'test_data' => $testData,
                    'debug_info' => [
                        'test_results_count' => TestResult::count(),
                        'questions_count' => Question::count(),
                        'question_options_count' => QuestionOption::count()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Test endpoint untuk debugging school data
     */
    public function testSchoolData()
    {
        try {
            $schools = School::select('id', 'npsn', 'name')->get();
            
            // Test validation logic
            $testNPSN = '12345678';
            $testNamaSekolah = 'SMA Negeri 1 Jakarta';
            
            $school = School::where('npsn', $testNPSN)->first();
            $validationResult = null;
            
            if ($school) {
                $validationResult = [
                    'request_nama_sekolah' => $testNamaSekolah,
                    'db_school_name' => $school->name,
                    'request_npsn' => $testNPSN,
                    'db_school_npsn' => $school->npsn,
                    'comparison' => [
                        'request_trimmed' => trim($testNamaSekolah),
                        'db_trimmed' => trim($school->name),
                        'request_lower' => strtolower(trim($testNamaSekolah)),
                        'db_lower' => strtolower(trim($school->name)),
                        'exact_match' => $testNamaSekolah === $school->name,
                        'trimmed_match' => trim($testNamaSekolah) === trim($school->name),
                        'lowercase_match' => strtolower(trim($testNamaSekolah)) === strtolower(trim($school->name)),
                        'length_request' => strlen($testNamaSekolah),
                        'length_db' => strlen($school->name),
                        'length_request_trimmed' => strlen(trim($testNamaSekolah)),
                        'length_db_trimmed' => strlen(trim($school->name))
                    ]
                ];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'School data retrieved successfully',
                'data' => [
                    'total_schools' => $schools->count(),
                    'schools' => $schools,
                    'test_validation' => $validationResult,
                    'raw_data' => [
                        'test_npsn' => $testNPSN,
                        'test_nama_sekolah' => $testNamaSekolah,
                        'school_found' => $school ? true : false
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test endpoint untuk debugging registration
     */
    public function testRegistration(Request $request)
    {
        try {
            // Simulate the exact validation logic from registerStudent
            $testData = [
                'nama_lengkap' => 'Ahmad Fadhillah',
                'nisn' => '123445667',
                'npsn_sekolah' => '12345678',
                'nama_sekolah' => 'SMA Negeri 1 Jakarta',
                'kelas' => 'XII IPA',
                'no_handphone' => '081234567890',
                'email' => 'ahmad@email.com',
                'no_orang_tua' => '081234567891'
            ];

            // Test validation step by step
            $school = School::where('npsn', $testData['npsn_sekolah'])->first();
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'NPSN sekolah tidak ditemukan',
                    'debug' => [
                        'requested_npsn' => $testData['npsn_sekolah'],
                        'available_npsn' => School::pluck('npsn')->toArray()
                    ]
                ], 404);
            }

            // Test name comparison
            $comparison = [
                'request_nama_sekolah' => $testData['nama_sekolah'],
                'db_school_name' => $school->name,
                'request_npsn' => $testData['npsn_sekolah'],
                'db_school_npsn' => $school->npsn,
                'comparison' => [
                    'exact_match' => $testData['nama_sekolah'] === $school->name,
                    'trimmed_match' => trim($testData['nama_sekolah']) === trim($school->name),
                    'lowercase_match' => strtolower(trim($testData['nama_sekolah'])) === strtolower(trim($school->name)),
                    'length_request' => strlen($testData['nama_sekolah']),
                    'length_db' => strlen($school->name),
                    'request_trimmed' => trim($testData['nama_sekolah']),
                    'db_trimmed' => trim($school->name),
                    'request_lower' => strtolower(trim($testData['nama_sekolah'])),
                    'db_lower' => strtolower(trim($school->name))
                ]
            ];

            $validationPassed = strtolower(trim($school->name)) === strtolower(trim($testData['nama_sekolah']));

            return response()->json([
                'success' => true,
                'message' => 'Registration test completed',
                'data' => [
                    'test_data' => $testData,
                    'school_found' => true,
                    'validation_passed' => $validationPassed,
                    'comparison' => $comparison,
                    'available_schools' => School::select('id', 'npsn', 'name')->get()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check existing test data
     */
    public function checkTestData()
    {
        try {
            // Check test_results table
            $testResults = \App\Models\TestResult::select('id', 'student_id', 'subjects', 'start_time', 'end_time', 'status', 'total_score')
                ->orderBy('id', 'asc')
                ->get();

            // Check test_answers table
            $testAnswers = \App\Models\TestAnswer::select('id', 'test_result_id', 'question_id', 'selected_option_id', 'answered_at')
                ->orderBy('test_result_id', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // Group answers by test_result_id
            $answersByTest = $testAnswers->groupBy('test_result_id');

            $totalTests = $testResults->count();
            $totalAnswers = $testAnswers->count();
            $lastTestId = $testResults->max('id') ?? 0;
            $nextTestId = $lastTestId + 1;

            return response()->json([
                'success' => true,
                'message' => 'Test data retrieved successfully',
                'data' => [
                    'summary' => [
                        'total_tests' => $totalTests,
                        'total_answers' => $totalAnswers,
                        'last_test_id' => $lastTestId,
                        'next_test_id_will_be' => $nextTestId
                    ],
                    'test_results' => $testResults->map(function($test) use ($answersByTest) {
                        $answers = $answersByTest->get($test->id, collect());
                        return [
                            'test_id' => $test->id,
                            'student_id' => $test->student_id,
                            'subjects' => $test->subjects,
                            'status' => $test->status,
                            'total_score' => $test->total_score,
                            'start_time' => $test->start_time,
                            'end_time' => $test->end_time,
                            'total_answers' => $answers->count(),
                            'answers' => $answers
                        ];
                    }),
                    'explanation' => [
                        'data_flow' => [
                            '1. POST /questions' => 'Creates record in test_results table',
                            '2. POST /submit-answers' => 'Creates records in test_answers table',
                            '3. test_id' => 'References test_results.id',
                            '4. Each answer' => 'Creates 1 row in test_answers table'
                        ],
                        'table_structure' => [
                            'test_results' => 'Main test session data',
                            'test_answers' => 'Individual student answers'
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get daftar mata pelajaran yang tersedia
     */
    public function getAvailableSubjects()
    {
        try {
            $subjects = Subject::where('is_active', true)
                ->select('name', 'code', 'is_required')
                ->orderBy('is_required', 'desc')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar mata pelajaran berhasil diambil',
                'data' => $subjects
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting subjects: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get daftar sekolah yang tersedia
     */
    public function getAvailableSchools()
    {
        try {
            $schools = School::select('id', 'npsn', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar sekolah berhasil diambil',
                'data' => $schools
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting schools: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get status siswa berdasarkan NISN
     */
    public function getStudentStatus($nisn)
    {
        try {
            $student = Student::where('nisn', $nisn)
                ->select('id', 'name', 'nisn', 'school_id', 'kelas', 'status') // Changed from nama_lengkap to name
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status siswa berhasil diambil',
                'data' => $student
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting student status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Hitung skor berdasarkan jawaban
     */
    private function calculateScores($testId)
    {
        try {
            $answers = TestAnswer::where('test_result_id', $testId)
                ->with(['question', 'selectedOption'])
                ->get();

            $scores = [];
            $subjects = [];

            foreach ($answers as $answer) {
                // Pastikan question dan selectedOption ada
                if (!$answer->question || !$answer->selectedOption) {
                    Log::warning('Missing question or selectedOption for answer', [
                        'answer_id' => $answer->id,
                        'question_id' => $answer->question_id,
                        'selected_option_id' => $answer->selected_option_id
                    ]);
                    continue;
                }

                // Gunakan subject dari question
                $subject = $answer->question->subject;
                if (!isset($subjects[$subject])) {
                    $subjects[$subject] = [
                        'correct' => 0,
                        'total' => 0,
                        'questions' => []
                    ];
                }

                $subjects[$subject]['total']++;
                
                if ($answer->selectedOption->is_correct) {
                    $subjects[$subject]['correct']++;
                }

                $subjects[$subject]['questions'][] = [
                    'question_id' => $answer->question_id,
                    'is_correct' => $answer->selectedOption->is_correct
                ];
            }

            foreach ($subjects as $subject => $data) {
                if ($data['total'] > 0) {
                    $score = ($data['correct'] / $data['total']) * 100;
                    $scores[] = [
                        'subject' => $subject,
                        'score' => round($score, 2),
                        'correct_answers' => $data['correct'],
                        'total_questions' => $data['total'],
                        'percentage' => round(($data['correct'] / $data['total']) * 100, 2)
                    ];
                }
            }

            return $scores;

        } catch (\Exception $e) {
            Log::error('Error calculating scores: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate recommendations based on student scores
     */
    private function generateRecommendations($scores)
    {
        try {
            if (empty($scores) || !is_array($scores)) {
                return [
                    'strengths' => [],
                    'weaknesses' => [],
                    'recommendations' => ['Konsultasi dengan guru BK untuk pemilihan jurusan']
                ];
            }

            // Get all active major recommendations
            $majorRecommendations = \App\Models\MajorRecommendation::where('is_active', true)->get();
            
            $recommendations = [];
            $strengths = [];
            $weaknesses = [];
            
            // Analyze strengths and weaknesses
            foreach ($scores as $score) {
                if (isset($score['subject']) && isset($score['percentage'])) {
                    if ($score['percentage'] >= 80) {
                        $strengths[] = $score['subject'];
                    } elseif ($score['percentage'] < 60) {
                        $weaknesses[] = $score['subject'];
                    }
                }
            }
            
            // Find matching majors
            foreach ($majorRecommendations as $major) {
                if ($major->matchesStudentScores($scores)) {
                    $confidenceScore = $major->calculateConfidenceScore($scores);
                    
                    $recommendations[] = [
                        'major' => $major->major_name,
                        'description' => $major->description,
                        'confidence_score' => round($confidenceScore, 2),
                        'career_prospects' => $major->career_prospects,
                        'requirements' => [
                            'min_score' => $major->min_score,
                            'max_score' => $major->max_score,
                            'required_subjects' => $major->required_subjects,
                            'preferred_subjects' => $major->preferred_subjects
                        ]
                    ];
                }
            }
            
            // Sort by confidence score (highest first)
            usort($recommendations, function ($a, $b) {
                return $b['confidence_score'] <=> $a['confidence_score'];
            });
            
            // Take top 3 recommendations
            $recommendations = array_slice($recommendations, 0, 3);
            
            // If no specific recommendations, provide general advice
            if (empty($recommendations)) {
                $recommendations[] = [
                    'major' => 'Konsultasi Guru BK',
                    'description' => 'Konsultasi dengan guru BK untuk pemilihan jurusan yang sesuai',
                    'confidence_score' => 0,
                    'career_prospects' => 'Akan ditentukan setelah konsultasi',
                    'requirements' => []
                ];
            }
            
            return [
                'strengths' => $strengths,
                'weaknesses' => $weaknesses,
                'recommendations' => $recommendations
            ];
            
        } catch (\Exception $e) {
            return [
                'strengths' => [],
                'weaknesses' => [],
                'recommendations' => ['Terjadi kesalahan dalam generate rekomendasi']
            ];
        }
    }

    /**
     * Test endpoint untuk debugging results
     */
    public function testResults($testId)
    {
        try {
            // Test basic query
            $testResult = TestResult::find($testId);
            
            if (!$testResult) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test result tidak ditemukan',
                    'debug' => [
                        'test_id' => $testId,
                        'all_test_results' => TestResult::select('id', 'student_id', 'status')->get()
                    ]
                ], 404);
            }

            // Test student relationship
            $student = $testResult->student;
            
            // Test school relationship
            $school = null;
            if ($student) {
                $school = $student->school;
            }

            // Test JSON fields
            $scores = null;
            $recommendations = null;
            try {
                $scores = json_decode($testResult->scores, true);
            } catch (\Exception $e) {
                $scores = 'JSON decode error: ' . $e->getMessage();
            }

            try {
                $recommendations = json_decode($testResult->recommendations, true);
            } catch (\Exception $e) {
                $recommendations = 'JSON decode error: ' . $e->getMessage();
            }

            return response()->json([
                'success' => true,
                'message' => 'Test results debug completed',
                'data' => [
                    'test_result' => [
                        'id' => $testResult->id,
                        'student_id' => $testResult->student_id,
                        'status' => $testResult->status,
                        'start_time' => $testResult->start_time,
                        'end_time' => $testResult->end_time,
                        'scores_raw' => $testResult->scores,
                        'recommendations_raw' => $testResult->recommendations,
                        'total_score' => $testResult->total_score
                    ],
                    'student_exists' => $student ? true : false,
                    'student_data' => $student ? [
                        'id' => $student->id,
                        'name' => $student->name,
                        'nisn' => $student->nisn,
                        'school_id' => $student->school_id
                    ] : null,
                    'school_exists' => $school ? true : false,
                    'school_data' => $school ? [
                        'id' => $school->id,
                        'name' => $school->name,
                        'npsn' => $school->npsn
                    ] : null,
                    'json_fields' => [
                        'scores_decoded' => $scores,
                        'recommendations_decoded' => $recommendations
                    ],
                    'relationships' => [
                        'student_has_school_method' => method_exists($student, 'school'),
                        'student_school_id' => $student ? $student->school_id : null
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Step by step results endpoint for debugging
     */
    public function stepResults($testId)
    {
        try {
            // Step 1: Get test result
            $testResult = TestResult::find($testId);
            
            if (!$testResult) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test result tidak ditemukan'
                ], 404);
            }

            // Step 2: Get student
            $student = $testResult->student;
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student tidak ditemukan'
                ], 404);
            }

            // Step 3: Get school
            $school = $student->school;
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'School tidak ditemukan'
                ], 404);
            }

            // Step 4: Decode JSON fields
            $scores = [];
            $recommendations = [];
            
            if ($testResult->scores) {
                $scores = json_decode($testResult->scores, true) ?? [];
            }
            
            if ($testResult->recommendations) {
                $recommendations = json_decode($testResult->recommendations, true) ?? [];
            }

            return response()->json([
                'success' => true,
                'message' => 'Step by step results retrieved',
                'data' => [
                    'test_info' => [
                        'test_id' => $testResult->id,
                        'status' => $testResult->status,
                        'start_time' => $testResult->start_time,
                        'end_time' => $testResult->end_time,
                        'total_score' => $testResult->total_score
                    ],
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->name,
                        'nisn' => $student->nisn,
                        'kelas' => $student->kelas
                    ],
                    'school' => [
                        'id' => $school->id,
                        'name' => $school->name,
                        'npsn' => $school->npsn
                    ],
                    'scores' => $scores,
                    'recommendations' => $recommendations
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'step' => 'Unknown'
            ], 500);
        }
    }

    /**
     * Very simple test endpoint
     */
    public function simpleTest()
    {
        return response()->json([
            'success' => true,
            'message' => 'Simple test working',
            'timestamp' => now()
        ]);
    }

    /**
     * Basic results endpoint without relationships
     */
    public function basicResults($testId)
    {
        try {
            $testResult = TestResult::select('id', 'student_id', 'status', 'start_time', 'end_time', 'total_score', 'scores', 'recommendations')
                ->where('id', $testId)
                ->first();
            
            if (!$testResult) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test result tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Basic results retrieved',
                'data' => [
                    'test_id' => $testResult->id,
                    'student_id' => $testResult->student_id,
                    'status' => $testResult->status,
                    'start_time' => $testResult->start_time,
                    'end_time' => $testResult->end_time,
                    'total_score' => $testResult->total_score,
                    'scores_raw' => $testResult->scores,
                    'recommendations_raw' => $testResult->recommendations
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Working results endpoint
     */
    public function workingResults($testId)
    {
        try {
            $testResult = TestResult::with(['student.school'])
                ->where('id', $testId)
                ->where('status', 'completed')
                ->first();

            if (!$testResult) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hasil tes tidak ditemukan atau belum selesai'
                ], 404);
            }

            // Decode JSON fields safely
            $scores = [];
            $recommendations = [];
            
            if ($testResult->scores) {
                $scores = is_string($testResult->scores) ? json_decode($testResult->scores, true) : $testResult->scores;
            }
            
            if ($testResult->recommendations) {
                $recommendations = is_string($testResult->recommendations) ? json_decode($testResult->recommendations, true) : $testResult->recommendations;
            }

            return response()->json([
                'success' => true,
                'message' => 'Hasil tes berhasil diambil',
                'data' => [
                    'student' => [
                        'nama_lengkap' => $testResult->student->name,
                        'nisn' => $testResult->student->nisn,
                        'nama_sekolah' => $testResult->student->school->name,
                        'kelas' => $testResult->student->kelas
                    ],
                    'test_info' => [
                        'test_id' => $testResult->id,
                        'start_time' => $testResult->start_time,
                        'end_time' => $testResult->end_time,
                        'duration' => $testResult->start_time->diffInMinutes($testResult->end_time),
                        'status' => $testResult->status
                    ],
                    'scores' => $scores,
                    'total_score' => $testResult->total_score ?? 0,
                    'average_score' => !empty($scores) ? round(($testResult->total_score ?? 0) / count($scores), 2) : 0,
                    'recommendations' => $recommendations
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check available major recommendations in database
     */
    public function checkMajorRecommendations()
    {
        try {
            $majors = \App\Models\MajorRecommendation::where('is_active', true)->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Major recommendations from database',
                'data' => [
                    'total_majors' => $majors->count(),
                    'majors' => $majors->map(function($major) {
                        return [
                            'id' => $major->id,
                            'major_name' => $major->major_name,
                            'description' => $major->description,
                            'min_score' => $major->min_score,
                            'max_score' => $major->max_score,
                            'required_subjects' => $major->required_subjects,
                            'preferred_subjects' => $major->preferred_subjects,
                            'career_prospects' => $major->career_prospects,
                            'is_active' => $major->is_active
                        ];
                    })
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking major recommendations: ' . $e->getMessage()
            ], 500);
        }
    }
}
