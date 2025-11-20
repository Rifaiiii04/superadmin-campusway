<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Student;
use App\Models\Question;
use App\Models\MajorRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class SuperAdminController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('SuperAdmin/Login', [
            'title' => 'SuperAdmin Login'
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('username', 'password');

        // Check if username exists first
        $admin = \App\Models\Admin::where('username', $credentials['username'])->first();

        if (!$admin) {
            return back()->withErrors([
                'username' => 'Username tidak ditemukan. Silakan periksa kembali username Anda.',
            ])->withInput($request->only('username'));
        }

        // Attempt authentication
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/super-admin/dashboard');
        }

        // If username exists but password is wrong
        return back()->withErrors([
            'password' => 'Password salah. Silakan periksa kembali password Anda.',
        ])->withInput($request->only('username'));
    }

    public function dashboard()
    {
        // Pastikan guard admin
        if (!Auth::guard('admin')->check()) {
            return redirect('/super-admin/login');
        }
        
        try {
            // Set execution time limit
            set_time_limit(60);
            
            // Cache dashboard stats for 10 minutes
            $stats = Cache::remember('superadmin_dashboard_stats', 600, function () {
                return [
                    'total_schools' => School::count(),
                    'total_students' => Student::count(),
                    'total_majors' => MajorRecommendation::where('is_active', true)->count(),
                    'total_questions' => Question::count(),
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

            return Inertia::render('SuperAdmin/Dashboard', [
                'title' => 'Dashboard SuperAdmin',
                'stats' => $stats,
                'recent_schools' => $recent_schools,
                'recent_students' => $recent_students,
            ]);

        } catch (\Exception $e) {
            // Log error
            \Log::error('SuperAdmin Dashboard Error: ' . $e->getMessage());
            
            return Inertia::render('SuperAdmin/Dashboard', [
                'title' => 'Dashboard SuperAdmin',
                'stats' => [
                    'total_schools' => 0,
                    'total_students' => 0,
                    'total_majors' => 0,
                    'total_questions' => 0,
                ],
                'recent_schools' => [],
                'recent_students' => [],
                'error' => 'Gagal memuat data dashboard'
            ]);
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
