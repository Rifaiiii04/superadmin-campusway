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

            return response()->json([
                'success' => true,
                'message' => 'Hasil tes berhasil diambil',
                'data' => [
                    'student' => [
                        'nama_lengkap' => $testResult->student->name, // Changed from nama_lengkap to name
                        'nisn' => $testResult->student->nisn,
                        'nama_sekolah' => $testResult->student->school->name, // Changed from nama_sekolah to school->name
                        'kelas' => $testResult->student->kelas
                    ],
                    'test_info' => [
                        'start_time' => $testResult->start_time,
                        'end_time' => $testResult->end_time,
                        'duration' => $testResult->start_time->diffInMinutes($testResult->end_time)
                    ],
                    'scores' => $scores,
                    'total_score' => $testResult->total_score,
                    'average_score' => round($testResult->total_score / count($scores), 2),
                    'recommendations' => $recommendations
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting results: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
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
            $schools = School::select('npsn', 'nama_sekolah', 'alamat', 'email', 'telepon')
                ->orderBy('nama_sekolah')
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
        $answers = TestAnswer::where('test_result_id', $testId)
            ->with(['question.subject', 'selectedOption'])
            ->get();

        $scores = [];
        $subjects = [];

        foreach ($answers as $answer) {
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
            $score = ($data['correct'] / $data['total']) * 100;
            $scores[] = [
                'subject' => $subject,
                'score' => round($score, 2),
                'correct_answers' => $data['correct'],
                'total_questions' => $data['total'],
                'percentage' => round(($data['correct'] / $data['total']) * 100, 2)
            ];
        }

        return $scores;
    }

    /**
     * Generate rekomendasi jurusan berdasarkan skor
     */
    private function generateRecommendations($scores)
    {
        $recommendations = [];
        
        // Analisis kekuatan dan kelemahan
        $strengths = [];
        $weaknesses = [];
        
        foreach ($scores as $score) {
            if ($score['percentage'] >= 80) {
                $strengths[] = $score['subject'];
            } elseif ($score['percentage'] < 60) {
                $weaknesses[] = $score['subject'];
            }
        }

        // Rekomendasi berdasarkan mata pelajaran unggulan
        if (in_array('Matematika', $strengths) && in_array('Fisika', $strengths)) {
            $recommendations[] = 'Teknik (Teknik Mesin, Teknik Elektro, Teknik Sipil)';
        }
        
        if (in_array('Biologi', $strengths) && in_array('Kimia', $strengths)) {
            $recommendations[] = 'Kedokteran, Farmasi, Biologi';
        }
        
        if (in_array('Bahasa Indonesia', $strengths) && in_array('Bahasa Inggris', $strengths)) {
            $recommendations[] = 'Sastra, Pendidikan Bahasa, Komunikasi';
        }
        
        if (in_array('Ekonomi', $strengths) && in_array('Matematika', $strengths)) {
            $recommendations[] = 'Ekonomi, Manajemen, Akuntansi';
        }

        // Rekomendasi umum jika tidak ada spesifik
        if (empty($recommendations)) {
            $recommendations[] = 'Pilih jurusan sesuai minat dan bakat';
            $recommendations[] = 'Konsultasi dengan guru BK untuk pemilihan jurusan';
        }

        return [
            'strengths' => $strengths,
            'weaknesses' => $weaknesses,
            'recommendations' => $recommendations
        ];
    }
}
