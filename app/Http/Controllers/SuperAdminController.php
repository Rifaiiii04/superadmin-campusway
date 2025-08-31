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

    public function importSchools(Request $request)
    {
        // Excel import functionality will be implemented later
        return back()->with('info', 'Fitur import Excel akan segera tersedia');
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
    public function questions()
    {
        $questions = Question::with('questionOptions')->paginate(10);
        return inertia('SuperAdmin/Questions', [
            'questions' => $questions,
            'auth' => [
                'user' => Auth::guard('admin')->user()
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

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/super-admin/login');
    }
}
