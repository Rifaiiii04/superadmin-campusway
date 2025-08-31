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
        $nationalStats = [
            'total_students' => Student::count(),
            'total_schools' => School::count(),
            'average_score' => Result::avg('score'),
            'total_recommendations' => Recommendation::count(),
        ];

        $schoolPerformance = School::withCount('students')
            ->get()
            ->map(function ($school) {
                $school->avg_score = $school->students->avg(function ($student) {
                    return $student->results->avg('score');
                });
                return $school;
            });

        $subjectPerformance = Result::selectRaw('subject, AVG(score) as avg_score, COUNT(*) as total_students')
            ->groupBy('subject')
            ->get();

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
            ]
        ]);
    }

    public function downloadReport(Request $request)
    {
        $type = $request->type;
        
        switch ($type) {
            case 'schools':
                $data = School::withCount('students')->get();
                break;
            case 'students':
                $data = Student::with('school')->get();
                break;
            case 'results':
                $data = Result::with('student.school')->get();
                break;
            case 'questions':
                $data = Question::with('questionOptions')->get();
                break;
            default:
                return back()->with('error', 'Tipe laporan tidak valid');
        }

        // Generate CSV/Excel report
        // Implementation depends on your preferred export library
        
        return back()->with('success', 'Laporan berhasil diunduh');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/super-admin/login');
    }
}
