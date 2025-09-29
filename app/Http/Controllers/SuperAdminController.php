<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Student;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Result;
use App\Models\Recommendation;
use App\Models\MajorRecommendation;
use App\Helpers\SMKSubjectHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
// use Maatwebsite\Excel\Facades\Excel;
// use App\Imports\SchoolsImport;

class SuperAdminController extends Controller
{
    public function showLogin()
    {
        return inertia('SuperAdmin/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/super-admin');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput($request->only('username'));
    }

    public function dashboard()
    {
        try {
            // Set execution time limit
            set_time_limit(60);
            
            // Cache dashboard stats for 10 minutes
            $stats = Cache::remember('superadmin_dashboard_stats', 600, function () {
                return [
                    'total_schools' => School::count(),
                    'total_students' => Student::count(),
                    'total_majors' => \App\Models\MajorRecommendation::where('is_active', true)->count(),
                ];
            });

            // Get recent schools and students with optimized queries
            $recent_schools = Cache::remember('recent_schools', 300, function () {
                return School::select('id', 'name', 'npsn', 'created_at')
                    ->latest()
                    ->take(5)
                    ->get();
            });

            $recent_students = Cache::remember('recent_students', 300, function () {
                return Student::select('id', 'name', 'nisn', 'school_id', 'created_at')
                    ->with(['school:id,name'])
                    ->latest()
                    ->take(5)
                    ->get();
            });

            // Get students per major for chart with optimized query
            $studentsPerMajor = Cache::remember('students_per_major', 600, function () {
                return \App\Models\StudentChoice::select('major_id')
                    ->selectRaw('COUNT(*) as student_count')
                    ->groupBy('major_id')
                    ->with(['major:id,major_name'])
                    ->get()
                    ->map(function($item) {
                        return [
                            'major_name' => $item->major->major_name ?? 'Unknown',
                            'student_count' => $item->student_count
                        ];
                    });
            });

            return inertia('SuperAdmin/Dashboard', [
                'stats' => $stats,
                'recent_schools' => $recent_schools,
                'recent_students' => $recent_students,
                'studentsPerMajor' => $studentsPerMajor,
                'auth' => [
                    'user' => Auth::guard('admin')->user()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            
            // Return minimal data on error
            return inertia('SuperAdmin/Dashboard', [
                'stats' => [
                    'total_schools' => 0,
                    'total_students' => 0,
                    'total_majors' => 0,
                ],
                'recent_schools' => [],
                'recent_students' => [],
                'studentsPerMajor' => [],
                'auth' => [
                    'user' => Auth::guard('admin')->user()
                ],
                'error' => 'Gagal memuat data dashboard. Silakan refresh halaman.'
            ]);
        }
    }

    // School Management
    public function schools(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        
        // Cache key based on page and search
        $cacheKey = "schools_page_{$page}_search_{$search}";
        
        $schools = Cache::remember($cacheKey, 300, function () use ($search) {
            $query = School::withCount('students');
            
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('npsn', 'like', "%{$search}%");
                });
            }
            
            return $query->paginate(10);
        });
        
        return inertia('SuperAdmin/Schools', [
            'schools' => $schools,
            'auth' => [
                'user' => Auth::guard('admin')->user()
            ]
        ]);
    }

    /**
     * Show school detail with students and their major choices
     */
    public function schoolDetail($id)
    {
        $school = School::with(['students.majorChoices.major'])->findOrFail($id);
        
        // Add students count
        $school->students_count = $school->students->count();
        
        return inertia('SuperAdmin/SchoolDetail', [
            'school' => $school,
            'auth' => [
                'user' => Auth::guard('admin')->user()
            ]
        ]);
    }

    /**
     * Show questions page (Coming Soon)
     */
    public function questions(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $subject = $request->get('subject', '');
        
        // Cache key based on parameters
        $cacheKey = "questions_page_{$page}_search_{$search}_subject_{$subject}";
        
        $questions = Cache::remember($cacheKey, 300, function () use ($search, $subject) {
            $query = Question::with(['questionOptions'])
                ->select(['id', 'subject', 'type', 'content', 'media_url', 'created_at']);
            
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('subject', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }
            
            if ($subject) {
                $query->where('subject', $subject);
            }
            
            return $query->paginate(20);
        });
        
        // Get unique subjects for filter
        $subjects = Cache::remember('question_subjects', 600, function () {
            return Question::distinct()->pluck('subject')->sort()->values();
        });
        
        return inertia('SuperAdmin/Questions', [
            'questions' => $questions,
            'subjects' => $subjects,
            'auth' => [
                'user' => Auth::guard('admin')->user()
            ]
        ]);
    }

    /**
     * Show results page (Coming Soon)
     */
    public function results()
    {
        return inertia('SuperAdmin/Results', [
            'auth' => [
                'user' => Auth::guard('admin')->user()
            ]
        ]);
    }

    public function storeSchool(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npsn' => 'required|string|max:8|unique:schools',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        School::create([
            'npsn' => $request->npsn,
            'name' => $request->name,
            'password_hash' => Hash::make($request->password),
        ]);

        // Clear related caches
        Cache::forget('superadmin_dashboard_stats');
        Cache::flush(); // Clear all school-related caches

        return back()->with('success', 'Sekolah berhasil ditambahkan');
    }



    public function updateSchool(Request $request, School $school)
    {
        $validator = Validator::make($request->all(), [
            'npsn' => 'required|string|max:8|unique:schools,npsn,' . $school->id,
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'npsn' => $request->npsn,
            'name' => $request->name,
        ];

        if ($request->password) {
            $data['password_hash'] = Hash::make($request->password);
        }

        $school->update($data);

        return back()->with('success', 'Sekolah berhasil diupdate');
    }

    public function deleteSchool(School $school)
    {
        $school->delete();
        return back()->with('success', 'Sekolah berhasil dihapus');
    }



    public function storeQuestion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'type' => 'required|in:Pilihan Ganda,Essay',
            'content' => 'required|string',
            'media_url' => 'nullable|string',
            'options' => 'required_if:type,Pilihan Ganda|array',
            'options.*.option_text' => 'required_if:type,Pilihan Ganda|string',
            'options.*.is_correct' => 'required_if:type,Pilihan Ganda|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $question = Question::create([
            'subject' => $request->subject,
            'type' => $request->type,
            'content' => $request->content,
            'media_url' => $request->media_url,
        ]);

        if ($request->type === 'Pilihan Ganda' && $request->options) {
            foreach ($request->options as $option) {
                $question->questionOptions()->create([
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct'],
                ]);
            }
        }

        return back()->with('success', 'Soal berhasil ditambahkan');
    }

    public function updateQuestion(Request $request, Question $question)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'type' => 'required|in:Pilihan Ganda,Essay',
            'content' => 'required|string',
            'media_url' => 'nullable|string',
            'options' => 'required_if:type,Pilihan Ganda|array',
            'options.*.option_text' => 'required_if:type,Pilihan Ganda|string',
            'options.*.is_correct' => 'required_if:type,Pilihan Ganda|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $question->update([
            'subject' => $request->subject,
            'type' => $request->type,
            'content' => $request->content,
            'media_url' => $request->media_url,
        ]);

        if ($request->type === 'Pilihan Ganda') {
            $question->questionOptions()->delete();
            foreach ($request->options as $option) {
                $question->questionOptions()->create([
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct'],
                ]);
            }
        }

        return back()->with('success', 'Soal berhasil diupdate');
    }

    public function deleteQuestion(Question $question)
    {
        $question->delete();
        return back()->with('success', 'Soal berhasil dihapus');
    }

    // Global Monitoring
    public function monitoring()
    {
        try {
            // Get all student choices
            $studentChoices = \App\Models\StudentChoice::with(['student.school', 'major'])
                ->get();

            // Calculate national statistics
            $totalSchools = \App\Models\School::count();
            $totalStudents = \App\Models\Student::count();
            $totalChoices = $studentChoices->count();
            
            // Count total students with choices
            $studentsWithChoices = $studentChoices->unique('student_id')->count();

            // Calculate school performance
            $schoolPerformance = \App\Models\School::withCount('students')
            ->get()
                ->map(function ($school) use ($studentChoices) {
                    $schoolChoices = $studentChoices->where('student.school_id', $school->id);
                    $studentsWithChoices = $schoolChoices->unique('student_id')->count();
                    
                    return [
                        'id' => $school->id,
                        'name' => $school->name,
                        'students_count' => $school->students_count,
                        'students_with_choices' => $studentsWithChoices,
                        'choices_count' => $schoolChoices->count()
                    ];
                })
                ->filter(function ($school) {
                    return $school['students_count'] > 0; // Only show schools with students
                })
                ->values();

            // Calculate major popularity
            $majorPopularity = [];
            foreach ($studentChoices as $choice) {
                $majorName = $choice->major->major_name ?? 'Unknown';
                if (!isset($majorPopularity[$majorName])) {
                    $majorPopularity[$majorName] = 0;
                }
                $majorPopularity[$majorName]++;
            }

            // Convert to array and sort by popularity
            $majorPopularityArray = [];
            foreach ($majorPopularity as $major => $count) {
                $majorPopularityArray[] = [
                    'major' => $major,
                    'total_students' => $count
                ];
            }
            usort($majorPopularityArray, function ($a, $b) {
                return $b['total_students'] <=> $a['total_students'];
            });

        return inertia('SuperAdmin/Monitoring', [
                'nationalStats' => [
                    'total_schools' => $totalSchools,
                    'total_students' => $totalStudents,
                    'total_choices' => $totalChoices,
                    'students_with_choices' => $studentsWithChoices
                ],
            'schoolPerformance' => $schoolPerformance,
                'majorPopularity' => array_slice($majorPopularityArray, 0, 10), // Top 10 majors
                'auth' => [
                    'user' => Auth::guard('admin')->user()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in monitoring: ' . $e->getMessage());
            return inertia('SuperAdmin/Monitoring', [
                'nationalStats' => [
                    'total_schools' => 0,
                    'total_students' => 0,
                    'total_choices' => 0,
                    'students_with_choices' => 0
                ],
                'schoolPerformance' => [],
                'majorPopularity' => [],
            'auth' => [
                'user' => Auth::guard('admin')->user()
            ]
        ]);
        }
    }

    /**
     * Get monitoring data from test results
     */
    public function getMonitoringData()
    {
        try {
            // Get all completed test results - using StudentChoice instead
            $testResults = \App\Models\StudentChoice::with(['student.school'])
                ->get();

            // Calculate national statistics
            $totalSchools = \App\Models\School::count();
            $totalStudents = \App\Models\Student::count();
            $totalTests = $testResults->count();
            
            // Calculate average score from all completed tests
            $totalScore = $testResults->sum('total_score');
            $averageScore = $totalTests > 0 ? round($totalScore / $totalTests, 2) : 0;
            
            // Count total recommendations
            $totalRecommendations = $testResults->whereNotNull('recommendations')->count();

            // Calculate school performance
            $schoolPerformance = \App\Models\School::withCount('students')
                ->get()
                ->map(function ($school) use ($testResults) {
                    $schoolTests = $testResults->where('student.school_id', $school->id);
                    $avgScore = $schoolTests->count() > 0 
                        ? round($schoolTests->avg('total_score'), 2) 
                        : 0;
                    
                    return [
                        'id' => $school->id,
                        'name' => $school->name,
                        'students_count' => $school->students_count,
                        'avg_score' => $avgScore,
                        'tests_count' => $schoolTests->count()
                    ];
                })
                ->filter(function ($school) {
                    return $school['tests_count'] > 0; // Only show schools with test results
                })
                ->values();

            // Calculate subject performance
            $subjectPerformance = [];
            $subjectScores = [];
            
            foreach ($testResults as $test) {
                $scores = is_string($test->scores) ? json_decode($test->scores, true) : $test->scores;
                
                if (is_array($scores)) {
                    foreach ($scores as $score) {
                        if (isset($score['subject']) && isset($score['score'])) {
                            $subject = $score['subject'];
                            if (!isset($subjectScores[$subject])) {
                                $subjectScores[$subject] = [];
                            }
                            $subjectScores[$subject][] = $score['score'];
                        }
                    }
                }
            }
            
            foreach ($subjectScores as $subject => $scores) {
                $avgScore = count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 0;
                $subjectPerformance[] = [
                    'subject' => $subject,
                    'total_students' => count($scores),
                    'avg_score' => $avgScore
                ];
            }

            // Sort by average score descending
            usort($subjectPerformance, function ($a, $b) {
                return $b['avg_score'] <=> $a['avg_score'];
            });

            // Calculate score distribution
            $scoreDistribution = [0, 0, 0, 0, 0]; // 0-60, 61-70, 71-80, 81-90, 91-100
            foreach ($testResults as $test) {
                $score = floatval($test->total_score);
                if ($score <= 60) $scoreDistribution[0]++;
                elseif ($score <= 70) $scoreDistribution[1]++;
                elseif ($score <= 80) $scoreDistribution[2]++;
                elseif ($score <= 90) $scoreDistribution[3]++;
                else $scoreDistribution[4]++;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'national_stats' => [
                        'total_schools' => $totalSchools,
                        'total_students' => $totalStudents,
                        'total_tests' => $totalTests,
                        'average_score' => $averageScore,
                        'total_recommendations' => $totalRecommendations
                    ],
                    'school_performance' => $schoolPerformance,
                    'subject_performance' => $subjectPerformance,
                    'score_distribution' => $scoreDistribution
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting monitoring data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Reports
    public function reports()
    {
        return inertia('SuperAdmin/Reports', [
            'auth' => [
                'user' => Auth::guard('admin')->user()
            ],
            'errors' => session('errors'),
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ]
        ]);
    }

    public function downloadReport(Request $request)
    {
        try {
            // Debug: log request data
            Log::info('Download report request:', $request->all());
            
            $request->validate([
                'type' => 'required|in:schools,students,results,questions',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

        $type = $request->type;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            Log::info('Download report type:', ['type' => $type, 'start_date' => $startDate, 'end_date' => $endDate]);
        
        switch ($type) {
            case 'schools':
                    $query = School::withCount('students');
                    if ($startDate && $endDate) {
                        $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    }
                    $data = $query->get();
                    $filename = 'Laporan_Sekolah_' . date('Y-m-d') . '.csv';
                break;
                    
            case 'students':
                    $query = Student::with('school');
                    if ($startDate && $endDate) {
                        $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    }
                    $data = $query->get();
                    $filename = 'Laporan_Siswa_' . date('Y-m-d') . '.csv';
                break;
                    
            case 'results':
                    $query = Result::with('student.school');
                    if ($startDate && $endDate) {
                        $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    }
                    $data = $query->get();
                    $filename = 'Laporan_Hasil_Ujian_' . date('Y-m-d') . '.csv';
                break;
                    
            case 'questions':
                    $query = Question::with('questionOptions');
                    if ($startDate && $endDate) {
                        $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    }
                    $data = $query->get();
                    $filename = 'Laporan_Bank_Soal_' . date('Y-m-d') . '.csv';
                break;
                    
            default:
                    return response()->json(['error' => 'Tipe laporan tidak valid'], 400);
            }

            // Generate CSV report dengan delimiter yang benar untuk Excel
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'no-cache',
            ];

            $callback = function() use ($data, $type) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Tambahkan header dengan styling yang lebih rapi
                fprintf($file, "LAPORAN " . strtoupper($type) . "\n");
                fprintf($file, "Tanggal Generate: " . date('d/m/Y H:i:s') . "\n");
                fprintf($file, "Total Data: " . $data->count() . " record(s)\n");
                fprintf($file, "\n");
                
                switch ($type) {
                    case 'schools':
                        // Gunakan semicolon sebagai delimiter untuk Excel
                        fwrite($file, "No;NPSN;Nama Sekolah;Alamat;Jumlah Siswa;Tanggal Terdaftar\n");
                        foreach ($data as $index => $school) {
                            $row = [
                                $index + 1,
                                $school->npsn,
                                $school->name,
                                $school->address ?? 'N/A',
                                $school->students_count,
                                $school->created_at ? $school->created_at->format('d/m/Y H:i') : 'N/A'
                            ];
                            fwrite($file, implode(';', $row) . "\n");
                        }
                        break;
                        
                    case 'students':
                        fwrite($file, "No;NISN;Nama Lengkap;Sekolah;Kelas;Jenis Kelamin;Email;Telepon;Alamat;Tanggal Terdaftar\n");
                        foreach ($data as $index => $student) {
                            $row = [
                                $index + 1,
                                $student->nisn,
                                $student->name,
                                $student->school ? $student->school->name : 'N/A',
                                $student->kelas,
                                $student->gender ?? 'N/A',
                                $student->email,
                                $student->phone,
                                $student->address ?? 'N/A',
                                $student->created_at ? $student->created_at->format('d/m/Y H:i') : 'N/A'
                            ];
                            fwrite($file, implode(';', $row) . "\n");
                        }
                        break;
                        
                    case 'results':
                        fwrite($file, "No;Nama Siswa;NISN;Sekolah;Mata Pelajaran;Skor;Status;Tanggal Ujian\n");
                        foreach ($data as $index => $result) {
                            // Tentukan status berdasarkan skor
                            $status = '';
                            if ($result->score >= 85) {
                                $status = 'Sangat Baik';
                            } elseif ($result->score >= 75) {
                                $status = 'Baik';
                            } elseif ($result->score >= 65) {
                                $status = 'Cukup';
                            } else {
                                $status = 'Kurang';
                            }
                            
                            $row = [
                                $index + 1,
                                $result->student ? $result->student->name : 'N/A',
                                $result->student ? $result->student->nisn : 'N/A',
                                $result->student && $result->student->school ? $result->student->school->name : 'N/A',
                                $result->subject,
                                $result->score,
                                $status,
                                $result->created_at ? $result->created_at->format('d/m/Y H:i') : 'N/A'
                            ];
                            fwrite($file, implode(';', $row) . "\n");
                        }
                        break;
                        
                    case 'questions':
                        fwrite($file, "No;Mata Pelajaran;Tipe;Soal;Media;Opsi A;Opsi B;Opsi C;Opsi D;Jawaban Benar;Tanggal Dibuat\n");
                        foreach ($data as $index => $question) {
                            // Ambil opsi jawaban dan urutkan
                            $options = $question->questionOptions->sortBy('id')->values();
                            
                            // Siapkan opsi A, B, C, D dengan default kosong
                            $optionA = $options->get(0) ? $options->get(0)->option_text : '';
                            $optionB = $options->get(1) ? $options->get(1)->option_text : '';
                            $optionC = $options->get(2) ? $options->get(2)->option_text : '';
                            $optionD = $options->get(3) ? $options->get(3)->option_text : '';
                            
                            // Tentukan jawaban benar (A, B, C, atau D)
                            $correctAnswer = '';
                            foreach ($options as $key => $option) {
                                if ($option->is_correct) {
                                    $correctAnswer = chr(65 + $key); // 65 = ASCII 'A'
                                    break;
                                }
                            }
                            
                            // Format media yang lebih jelas
                            $media = 'Tidak ada';
                            if ($question->media_url && !empty($question->media_url)) {
                                $media = 'Ya';
                            }
                            
                            $row = [
                                $index + 1,
                                $question->subject,
                                $question->type,
                                $question->content,
                                $media,
                                $optionA,
                                $optionB,
                                $optionC,
                                $optionD,
                                $correctAnswer,
                                $question->created_at ? $question->created_at->format('d/m/Y H:i') : 'N/A'
                            ];
                            fwrite($file, implode(';', $row) . "\n");
                        }
                        break;
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Download report error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengunduh laporan: ' . $e->getMessage()], 500);
        }
    }

    // Test method untuk debugging download
    public function testDownload()
    {
        try {
            $data = School::withCount('students')->get();
            
            $filename = 'test_laporan_sekolah.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
                
                fputcsv($file, ['ID', 'NPSN', 'Nama Sekolah', 'Jumlah Siswa', 'Tanggal Dibuat']);
                foreach ($data as $school) {
                    fputcsv($file, [
                        $school->id,
                        $school->npsn,
                        $school->name,
                        $school->students_count,
                        $school->created_at ? $school->created_at->format('Y-m-d H:i:s') : 'N/A'
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Test error: ' . $e->getMessage()], 500);
        }
    }

    public function importTest()
    {
        return response()->json([
            'message' => 'Import test endpoint accessible',
            'timestamp' => now(),
            'status' => 'success'
        ]);
    }

    public function importQuestions(Request $request)
    {
        try {
            // Debug: log request data
            Log::info('Import questions request received', [
                'all_data' => $request->all(),
                'has_file' => $request->hasFile('file'),
                'file_name' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'no file',
                'file_size' => $request->file('file') ? $request->file('file')->getSize() : 'no file'
            ]);

            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:2048',
            ]);

            $file = $request->file('file');
            $content = file_get_contents($file->getPathname());
            $lines = explode("\n", $content);
            
            // Skip header
            $headers = array_map('trim', explode(';', $lines[0]));
            $expectedHeaders = ['No', 'Mata Pelajaran', 'Tipe', 'Soal', 'Media', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Jawaban Benar', 'Tanggal Dibuat'];
            
            // Validasi header
            if (count(array_intersect($headers, $expectedHeaders)) < count($expectedHeaders)) {
                return back()->withErrors(['file' => 'Format header tidak sesuai. Gunakan template yang disediakan.']);
            }

            $imported = 0;
            $errors = [];
            
            // Process data rows
            for ($i = 1; $i < count($lines); $i++) {
                if (empty(trim($lines[$i]))) continue;
                
                $values = array_map('trim', explode(';', $lines[$i]));
                if (count($values) < 10) continue; // Skip incomplete rows
                
                try {
                    // Map values to columns
                    $data = array_combine($headers, $values);
                    
                    // Validasi data
                    if (empty($data['Mata Pelajaran']) || empty($data['Soal']) || empty($data['Tipe'])) {
                        $errors[] = "Baris " . ($i + 1) . ": Data tidak lengkap";
                        continue;
                    }
                    
                    if ($data['Tipe'] !== 'Pilihan Ganda') {
                        $errors[] = "Baris " . ($i + 1) . ": Hanya soal pilihan ganda yang didukung";
                        continue;
                    }
                    
                    // Validasi opsi jawaban
                    $options = [
                        $data['Opsi A'] ?? '',
                        $data['Opsi B'] ?? '',
                        $data['Opsi C'] ?? '',
                        $data['Opsi D'] ?? ''
                    ];
                    
                    if (empty(array_filter($options))) {
                        $errors[] = "Baris " . ($i + 1) . ": Minimal harus ada satu opsi jawaban";
                        continue;
                    }
                    
                    // Validasi jawaban benar
                    $correctAnswer = strtoupper(trim($data['Jawaban Benar'] ?? ''));
                    if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
                        $errors[] = "Baris " . ($i + 1) . ": Jawaban benar harus A, B, C, atau D";
                        continue;
                    }
                    
                    // Buat soal baru
                    $question = Question::create([
                        'subject' => $data['Mata Pelajaran'],
                        'type' => $data['Tipe'],
                        'content' => $data['Soal'],
                        'media_url' => ($data['Media'] === 'Ya') ? 'placeholder' : null,
                    ]);
                    
                    // Buat opsi jawaban
                    foreach ($options as $index => $optionText) {
                        if (!empty($optionText)) {
                            QuestionOption::create([
                                'question_id' => $question->id,
                                'option_text' => $optionText,
                                'is_correct' => ($correctAnswer === chr(65 + $index)), // A=0, B=1, C=2, D=3
                            ]);
                        }
                    }
                    
                    $imported++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
                }
            }
            
            if ($imported > 0) {
                $message = "Berhasil mengimport {$imported} soal.";
                if (count($errors) > 0) {
                    $message .= " Terdapat " . count($errors) . " error yang di-skip.";
                }
                
                Log::info('Import questions completed successfully', [
                    'imported' => $imported,
                    'errors_count' => count($errors),
                    'message' => $message
                ]);
                
                // Return JSON response untuk AJAX request
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'imported' => $imported,
                        'errors_count' => count($errors)
                    ]);
                }
                
                return back()->with('success', $message);
            } else {
                Log::warning('Import questions failed - no questions imported');
                
                // Return JSON response untuk AJAX request
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada soal yang berhasil diimport. Periksa format file.',
                        'errors' => ['file' => 'Tidak ada soal yang berhasil diimport. Periksa format file.']
                    ], 422);
                }
                
                return back()->withErrors(['file' => 'Tidak ada soal yang berhasil diimport. Periksa format file.']);
            }
            
        } catch (\Exception $e) {
            Log::error('Import questions error: ' . $e->getMessage());
            
            // Return JSON response untuk AJAX request
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage(),
                    'errors' => ['file' => 'Terjadi kesalahan saat import: ' . $e->getMessage()]
                ], 500);
            }
            
            return back()->withErrors(['file' => 'Terjadi kesalahan saat import: ' . $e->getMessage()]);
        }
    }

    public function importSchools(Request $request)
    {
        try {
            // Debug: log request data
            Log::info('Import schools request received', [
                'all_data' => $request->all(),
                'has_file' => $request->hasFile('file'),
                'file_name' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'no file',
                'file_size' => $request->file('file') ? $request->file('file')->getSize() : 'no file'
            ]);

            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:2048',
            ]);

            $file = $request->file('file');
            $content = file_get_contents($file->getPathname());
            $lines = explode("\n", $content);
            
            // Debug: log file content
            Log::info('File content analysis', [
                'total_lines' => count($lines),
                'first_line' => $lines[0] ?? 'empty',
                'second_line' => $lines[1] ?? 'empty',
                'content_preview' => substr($content, 0, 500)
            ]);
            
            // Skip header
            $headers = array_map('trim', explode(';', $lines[0]));
            $requiredHeaders = ['NPSN', 'Nama Sekolah', 'Password'];
            $optionalHeaders = ['Alamat', 'Email', 'Telepon'];
            
            Log::info('Headers analysis', [
                'found_headers' => $headers,
                'required_headers' => $requiredHeaders,
                'missing_headers' => array_diff($requiredHeaders, $headers)
            ]);
            
            // Validate required headers
            $missingRequiredHeaders = array_diff($requiredHeaders, $headers);
            if (!empty($missingRequiredHeaders)) {
                Log::warning('Import schools failed - missing required headers', ['missing' => $missingRequiredHeaders]);
                return response()->json([
                    'success' => false,
                    'message' => 'Header wajib tidak ditemukan: ' . implode(', ', $missingRequiredHeaders),
                    'errors' => ['file' => 'Header wajib tidak ditemukan: ' . implode(', ', $missingRequiredHeaders)]
                ], 422);
            }
            
            $imported = 0;
            $errors = [];
            $processedRows = 0;
            
            // Process data rows
            for ($i = 1; $i < count($lines); $i++) {
                if (empty(trim($lines[$i]))) {
                    Log::info("Skipping empty line at index {$i}");
                    continue;
                }
                
                $values = array_map('trim', explode(';', $lines[$i]));
                Log::info("Processing row {$i}", [
                    'values' => $values,
                    'values_count' => count($values),
                    'line_content' => $lines[$i]
                ]);
                
                if (count($values) < 3) {
                    Log::warning("Row {$i} has insufficient values", ['values_count' => count($values), 'min_required' => 3]);
                    continue;
                }
                
                $processedRows++;
                
                // Create row data with available headers
                $rowData = [];
                foreach ($headers as $index => $header) {
                    $rowData[$header] = isset($values[$index]) ? $values[$index] : '';
                }
                
                Log::info("Row {$i} data parsed", ['row_data' => $rowData]);
                
                // Validate required fields
                if (empty($rowData['NPSN']) || empty($rowData['Nama Sekolah']) || empty($rowData['Password'])) {
                    $errorMsg = "Baris " . ($i + 1) . ": NPSN, Nama Sekolah, dan Password wajib diisi";
                    Log::warning("Row {$i} validation failed - missing required fields", [
                        'npsn' => $rowData['NPSN'],
                        'nama_sekolah' => $rowData['Nama Sekolah'],
                        'password' => $rowData['Password']
                    ]);
                    $errors[] = $errorMsg;
                    continue;
                }
                
                // Validate NPSN format (8 digit angka)
                if (!preg_match('/^\d{8}$/', $rowData['NPSN'])) {
                    $errorMsg = "Baris " . ($i + 1) . ": NPSN harus 8 digit angka";
                    Log::warning("Row {$i} validation failed - invalid NPSN format", ['npsn' => $rowData['NPSN']]);
                    $errors[] = $errorMsg;
                    continue;
                }
                
                // Check if NPSN already exists
                $existingSchool = School::where('npsn', $rowData['NPSN'])->first();
                if ($existingSchool) {
                    $errorMsg = "Baris " . ($i + 1) . ": NPSN {$rowData['NPSN']} sudah ada (ID: {$existingSchool->id})";
                    Log::warning("Row {$i} validation failed - NPSN already exists", [
                        'npsn' => $rowData['NPSN'],
                        'existing_school_id' => $existingSchool->id,
                        'existing_school_name' => $existingSchool->name
                    ]);
                    $errors[] = $errorMsg;
                    continue;
                }
                
                try {
                    // Create school
                    $school = School::create([
                        'npsn' => $rowData['NPSN'],
                        'name' => trim($rowData['Nama Sekolah']),
                        'password_hash' => Hash::make($rowData['Password']),
                    ]);
                    
                    $imported++;
                    Log::info('School imported successfully', ['school_id' => $school->id, 'npsn' => $school->npsn, 'name' => $school->name]);
                    
                } catch (\Exception $e) {
                    Log::error('Failed to import school at row ' . ($i + 1), ['error' => $e->getMessage(), 'data' => $rowData]);
                    $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
                }
            }
            
            // Log final summary
            Log::info('Import schools processing completed', [
                'total_lines' => count($lines),
                'processed_rows' => $processedRows,
                'imported' => $imported,
                'errors_count' => count($errors),
                'errors' => $errors
            ]);

            if ($imported > 0) {
                $message = "Berhasil mengimport {$imported} sekolah.";
                if (count($errors) > 0) {
                    $message .= " Terdapat " . count($errors) . " error yang di-skip.";
                }

                Log::info('Import schools completed successfully', [
                    'imported' => $imported,
                    'errors_count' => count($errors),
                    'message' => $message
                ]);

                // Return JSON response untuk AJAX request
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'imported' => $imported,
                        'errors_count' => count($errors)
                    ]);
                }

                return back()->with('success', $message);
            } else {
                Log::warning('Import schools failed - no schools imported', [
                    'total_lines' => count($lines),
                    'processed_rows' => $processedRows,
                    'errors' => $errors
                ]);

                // Return JSON response untuk AJAX request
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada sekolah yang berhasil diimport. Periksa format file dan log untuk detail error.',
                        'errors' => ['file' => 'Tidak ada sekolah yang berhasil diimport. Periksa format file dan log untuk detail error.']
                    ], 422);
                }

                return back()->withErrors(['file' => 'Tidak ada sekolah yang berhasil diimport. Periksa format file dan log untuk detail error.']);
            }

        } catch (\Exception $e) {
            Log::error('Import schools error: ' . $e->getMessage());

            // Return JSON response untuk AJAX request
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage(),
                    'errors' => ['file' => 'Terjadi kesalahan saat import: ' . $e->getMessage()]
                ], 500);
            }

            return back()->withErrors(['file' => 'Terjadi kesalahan saat import: ' . $e->getMessage()]);
        }
    }

    public function importSchoolsTest()
    {
        // Test method untuk debug
        $testData = [
            'npsn' => '12345678',
            'name' => 'Test School',
            'password_hash' => Hash::make('test123')
        ];
        
        try {
            $school = School::create($testData);
            return response()->json([
                'success' => true,
                'message' => 'Test school created successfully',
                'school' => $school
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/super-admin/login');
    }

    /**
     * Show major recommendations management page
     */
    public function majorRecommendations()
    {
        $majorRecommendations = \App\Models\MajorRecommendation::with(['majorSubjectMappings.subject'])
            ->orderBy('major_name')
            ->get()
            ->map(function($major) {
                // Tentukan education level berdasarkan rumpun ilmu dan nama jurusan
                $educationLevel = $this->determineEducationLevelForMajor($major);
                
                // Dapatkan mata pelajaran wajib (3 untuk semua) - gunakan 'Umum' karena mata pelajaran wajib berlaku untuk semua jenjang
                $mandatorySubjects = \App\Models\Subject::where('subject_type', 'Wajib')
                    ->where('education_level', 'Umum')
                    ->pluck('name')
                    ->toArray();
                
                // Dapatkan mata pelajaran pilihan dari database preferred_subjects field
                $preferredSubjects = $major->preferred_subjects;
                if (is_string($preferredSubjects)) {
                    $decoded = json_decode($preferredSubjects, true);
                    $optionalSubjects = is_array($decoded) ? $decoded : [];
                } else {
                    $optionalSubjects = is_array($preferredSubjects) ? $preferredSubjects : [];
                }
                
                return [
                    'id' => $major->id,
                    'major_name' => $major->major_name,
                    'description' => $major->description,
                    'rumpun_ilmu' => $major->category,
                    'education_level' => $educationLevel,
                    'mandatory_subjects' => $mandatorySubjects,
                    'preferred_subjects' => $optionalSubjects,
                    'kurikulum_merdeka_subjects' => $this->parseJsonField($major->kurikulum_merdeka_subjects),
                    'kurikulum_2013_ipa_subjects' => $this->parseJsonField($major->kurikulum_2013_ipa_subjects),
                    'kurikulum_2013_ips_subjects' => $this->parseJsonField($major->kurikulum_2013_ips_subjects),
                    'kurikulum_2013_bahasa_subjects' => $this->parseJsonField($major->kurikulum_2013_bahasa_subjects),
                    'career_prospects' => $major->career_prospects ?? '',
                    'is_active' => $major->is_active,
                    'created_at' => $major->created_at,
                    'updated_at' => $major->updated_at
                ];
            });
        
        // Get available subjects from database
        $availableSubjects = \App\Models\Subject::where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
        
        // Debug: Log the data being sent to frontend
        $firstMajor = $majorRecommendations->first();
        Log::info('Major Recommendations Data:', [
            'count' => $majorRecommendations->count(),
            'first_major_name' => $firstMajor['major_name'] ?? 'None',
            'first_major_preferred_subjects' => $firstMajor['preferred_subjects'] ?? 'None',
            'available_subjects_count' => count($availableSubjects)
        ]);
        
        return inertia('SuperAdmin/MajorRecommendations', [
            'majorRecommendations' => $majorRecommendations,
            'availableSubjects' => $availableSubjects,
            'auth' => [
                'user' => Auth::guard('admin')->user()
            ]
        ]);
    }
    
    private function determineEducationLevel($rumpunIlmu)
    {
        // Tentukan education level berdasarkan rumpun ilmu
        $smaRumpun = ['ILMU ALAM', 'ILMU SOSIAL', 'ILMU BUDAYA', 'ILMU TERAPAN', 'ILMU FORMAL'];
        
        if (in_array($rumpunIlmu, $smaRumpun)) {
            return 'SMA/MA';
        } else {
            return 'SMK/MAK';
        }
    }
    
    /**
     * Parse JSON field that might be double-encoded
     */
    private function parseJsonField($field)
    {
        if (is_array($field)) {
            return $field;
        }
        
        if (is_string($field)) {
            $decoded = json_decode($field, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return [];
    }

    /**
     * Determine education level for specific major (handles HUMANIORA special case)
     */
    private function determineEducationLevelForMajor($major)
    {
        $rumpunIlmu = $major->category;
        $majorName = $major->major_name;
        
        // Khusus untuk HUMANIORA, periksa nama jurusan
        if ($rumpunIlmu === 'Humaniora') {
            $smaHumanioraMajors = ['Seni', 'Linguistik', 'Filsafat', 'Sejarah', 'Sastra'];
            if (in_array($majorName, $smaHumanioraMajors)) {
                return 'SMA/MA';
            } else {
                return 'SMK/MAK';
            }
        }
        
        // Untuk rumpun ilmu lain, gunakan logika normal
        $smaRumpun = ['Ilmu Alam', 'Ilmu Sosial', 'Ilmu Terapan', 'Ilmu Formal'];
        if (in_array($rumpunIlmu, $smaRumpun)) {
            return 'SMA/MA';
        } else {
            return 'SMK/MAK';
        }
    }

    /**
     * Store new major recommendation
     */
    public function storeMajorRecommendation(Request $request)
    {
        try {
            $request->validate([
                'major_name' => 'required|string|max:255|unique:major_recommendations,major_name',
                'rumpun_ilmu' => 'required|string|in:HUMANIORA,ILMU SOSIAL,ILMU ALAM,ILMU FORMAL,ILMU TERAPAN',
                'description' => 'nullable|string',
                'required_subjects' => 'nullable|array',
                'required_subjects.*' => 'string',
                'preferred_subjects' => 'nullable|array',
                'preferred_subjects.*' => 'string',
                'kurikulum_merdeka_subjects' => 'nullable|array',
                'kurikulum_merdeka_subjects.*' => 'string',
                'kurikulum_2013_ipa_subjects' => 'nullable|array',
                'kurikulum_2013_ipa_subjects.*' => 'string',
                'kurikulum_2013_ips_subjects' => 'nullable|array',
                'kurikulum_2013_ips_subjects.*' => 'string',
                'kurikulum_2013_bahasa_subjects' => 'nullable|array',
                'kurikulum_2013_bahasa_subjects.*' => 'string',
                'career_prospects' => 'nullable|string',
                'is_active' => 'boolean'
            ]);

            // Set required subjects to be the same for all majors
            $data = $request->all();
            $data['required_subjects'] = [
                'Matematika',
                'Bahasa Inggris',
                'Bahasa Indonesia'
            ];

            \App\Models\MajorRecommendation::create($data);

            return redirect()->back()->with('success', 'Rekomendasi jurusan berhasil ditambahkan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error in storeMajorRecommendation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan database. Silakan coba lagi.');
        } catch (\Exception $e) {
            Log::error('Error in storeMajorRecommendation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    /**
     * Update major recommendation
     */
    public function updateMajorRecommendation(Request $request, $id)
    {
        try {
            // Debug: Log the incoming request data
            Log::info('Update Major Request Data:', $request->all());
            
            // Validate first
            $request->validate([
                'major_name' => 'required|string|max:255|unique:major_recommendations,major_name,' . $id,
                'rumpun_ilmu' => 'required|string|in:HUMANIORA,ILMU SOSIAL,ILMU ALAM,ILMU FORMAL,ILMU TERAPAN',
                'description' => 'nullable|string',
                'required_subjects' => 'nullable|array',
                'required_subjects.*' => 'string',
                'preferred_subjects' => 'nullable|array',
                'preferred_subjects.*' => 'string',
                'kurikulum_merdeka_subjects' => 'nullable|array',
                'kurikulum_merdeka_subjects.*' => 'string',
                'kurikulum_2013_ipa_subjects' => 'nullable|array',
                'kurikulum_2013_ipa_subjects.*' => 'string',
                'kurikulum_2013_ips_subjects' => 'nullable|array',
                'kurikulum_2013_ips_subjects.*' => 'string',
                'kurikulum_2013_bahasa_subjects' => 'nullable|array',
                'kurikulum_2013_bahasa_subjects.*' => 'string',
                'career_prospects' => 'nullable|string',
                'is_active' => 'boolean'
            ]);

            // Then find the major
            $major = \App\Models\MajorRecommendation::findOrFail($id);

            // Set required subjects to be the same for all majors
            $data = $request->all();
            $data['required_subjects'] = [
                'Matematika',
                'Bahasa Inggris',
                'Bahasa Indonesia'
            ];

            // Debug: Log the data being updated
            Log::info('Update Major Data:', $data);

            $major->update($data);

            return redirect()->back()->with('success', 'Rekomendasi jurusan berhasil diupdate');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan.');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error in updateMajorRecommendation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan database. Silakan coba lagi.');
        } catch (\Exception $e) {
            Log::error('Error in updateMajorRecommendation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    /**
     * Delete major recommendation
     */
    public function deleteMajorRecommendation($id)
    {
        $major = \App\Models\MajorRecommendation::findOrFail($id);
        $major->delete();

        return redirect()->back()->with('success', 'Rekomendasi jurusan berhasil dihapus');
    }

    /**
     * Toggle major recommendation status
     */
    public function toggleMajorRecommendation($id)
    {
        $major = \App\Models\MajorRecommendation::findOrFail($id);
        $major->update(['is_active' => !$major->is_active]);

        return redirect()->back()->with('success', 'Status rekomendasi jurusan berhasil diubah');
    }

    /**
     * Export major recommendations to CSV
     */
    public function exportMajorRecommendations()
    {
        $majorRecommendations = \App\Models\MajorRecommendation::all();

        $filename = 'major_recommendations_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($majorRecommendations) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($file, [
                'ID',
                'Nama Jurusan',
                'Deskripsi',
                'Mata Pelajaran Wajib',
                'Mata Pelajaran Pilihan',
                'Kurikulum Merdeka',
                'Kurikulum 2013 - IPA',
                'Kurikulum 2013 - IPS',
                'Kurikulum 2013 - Bahasa',
                'Prospek Karir',
                'Aktif',
                'Dibuat Pada',
                'Diperbarui Pada'
            ], ';'); // Use semicolon as delimiter

            // Data
            foreach ($majorRecommendations as $major) {
                fputcsv($file, [
                    $major->id,
                    $major->major_name,
                    $major->description,
                    implode(', ', $major->required_subjects ?? []),
                    implode(', ', $major->preferred_subjects ?? []),
                    implode(', ', $major->kurikulum_merdeka_subjects ?? []),
                    implode(', ', $major->kurikulum_2013_ipa_subjects ?? []),
                    implode(', ', $major->kurikulum_2013_ips_subjects ?? []),
                    implode(', ', $major->kurikulum_2013_bahasa_subjects ?? []),
                    $major->career_prospects,
                    $major->is_active ? 'Ya' : 'Tidak',
                    $major->created_at,
                    $major->updated_at
                ], ';'); // Use semicolon as delimiter
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}
