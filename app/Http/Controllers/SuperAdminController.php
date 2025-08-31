<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Student;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Result;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        $stats = [
            'total_schools' => School::count(),
            'total_students' => Student::count(),
            'total_questions' => Question::count(),
            'total_results' => Result::count(),
        ];

        $recent_schools = School::latest()->take(5)->get();
        $recent_students = Student::with('school')->latest()->take(5)->get();

        return inertia('SuperAdmin/Dashboard', [
            'stats' => $stats,
            'recent_schools' => $recent_schools,
            'recent_students' => $recent_students,
            'auth' => [
                'user' => Auth::guard('admin')->user()
            ]
        ]);
    }

    // School Management
    public function schools()
    {
        $schools = School::withCount('students')->paginate(10);
        return inertia('SuperAdmin/Schools', [
            'schools' => $schools,
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

    // Question Bank Management
    public function questions(Request $request)
    {
        $query = Question::with('questionOptions');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('questionOptions', function($subQ) use ($search) {
                      $subQ->where('option_text', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by subject
        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Get unique subjects for filter dropdown
        $subjects = Question::distinct()->pluck('subject')->filter()->values();
        
        // Sorting
        $sortBy = $request->get('sort_by', 'subject'); // Default sort by subject
        $sortOrder = $request->get('sort_order', 'asc'); // Default ascending
        
        switch ($sortBy) {
            case 'subject':
                $query->orderBy('subject', $sortOrder);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortOrder);
                break;
            case 'type':
                $query->orderBy('type', $sortOrder);
                break;
            default:
                $query->orderBy('subject', 'asc'); // Fallback to subject ASC
        }
        
        $questions = $query->paginate(20);
        
        return inertia('SuperAdmin/QuestionsFixed', [
            'questions' => $questions,
            'subjects' => $subjects,
            'filters' => [
                'search' => $request->search,
                'subject' => $request->subject,
                'type' => $request->type,
                'sort_by' => $request->get('sort_by', 'subject'),
                'sort_order' => $request->get('sort_order', 'asc'),
            ],
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
        // National Statistics
        $nationalStats = [
            'total_students' => Student::count(),
            'total_schools' => School::count(),
            'average_score' => Result::avg('score') ?? 0,
            'total_recommendations' => Recommendation::count(),
        ];

        // School Performance - Get schools with student count and average scores
        $schoolPerformance = School::withCount('students')
            ->with(['students.results'])
            ->get()
            ->map(function ($school) {
                $avgScore = 0;
                if ($school->students_count > 0) {
                    $totalScore = 0;
                    $totalResults = 0;
                    
                    foreach ($school->students as $student) {
                        if ($student->results->count() > 0) {
                            $totalScore += $student->results->sum('score');
                            $totalResults += $student->results->count();
                        }
                    }
                    
                    if ($totalResults > 0) {
                        $avgScore = round($totalScore / $totalResults, 2);
                    }
                }
                
                return [
                    'id' => $school->id,
                    'name' => $school->name,
                    'students_count' => $school->students_count,
                    'avg_score' => $avgScore,
                ];
            })
            ->filter(function ($school) {
                return $school['students_count'] > 0; // Only show schools with students
            })
            ->values();

        // Subject Performance - Get subjects with average scores and student counts
        $subjectPerformance = Result::selectRaw('
                subject, 
                ROUND(AVG(CAST(score AS DECIMAL(5,2))), 2) as avg_score, 
                COUNT(DISTINCT student_id) as total_students
            ')
            ->whereNotNull('subject')
            ->where('subject', '!=', '')
            ->groupBy('subject')
            ->get()
            ->map(function ($subject) {
                return [
                    'subject' => $subject->subject,
                    'total_students' => $subject->total_students,
                    'avg_score' => round($subject->avg_score, 2),
                ];
            });

        return inertia('SuperAdmin/Monitoring', [
            'nationalStats' => $nationalStats,
            'schoolPerformance' => $schoolPerformance,
            'subjectPerformance' => $subjectPerformance,
            'auth' => [
                'user' => Auth::guard('admin')->user()
            ]
        ]);
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
}
