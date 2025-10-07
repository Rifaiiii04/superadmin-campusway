<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

// ===========================================
// SUPER ADMIN ROUTES WITH UI RENDERING
// ===========================================

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'framework' => 'Laravel', 
        'version' => app()->version(),
        'timestamp' => now()->toISOString()
    ]);
});

// Test endpoint
Route::get('/test', function () {
    return response()->json([
        'status' => 'SUCCESS',
        'message' => 'Laravel is working!',
        'bootstrap' => 'successful',
        'timestamp' => now()->toISOString()
    ]);
});

// SuperAdmin Login Page (UI)
Route::get('/login', function () {
    try {
        return Inertia::render('SuperAdmin/Login', [
            'title' => 'SuperAdmin Login',
            'version' => app()->version(),
        ]);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// SuperAdmin Login Process
Route::post('/login', function (Request $request) {
    try {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('username', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid credentials.',
        ])->withInput();
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// SuperAdmin Dashboard (UI) - Temporary bypass auth for testing
Route::get('/dashboard', function () {
    try {
        // Debug: Log dashboard access attempt
        \Log::info('Dashboard access attempt, admin check: ' . (Auth::guard('admin')->check() ? 'true' : 'false'));
        
        // Temporary bypass auth for testing navigation
        // if (!Auth::guard('admin')->check()) {
        //     \Log::info('Admin not authenticated, redirecting to login');
        //     return redirect('/login');
        // }
        
        \Log::info('Rendering SuperAdmin Dashboard');
        return Inertia::render('SuperAdmin/Dashboard', [
            'title' => 'SuperAdmin Dashboard',
            'user' => Auth::guard('admin')->user() ?: (object)['username' => 'Test Admin', 'name' => 'Test Admin'],
            'stats' => [
                'total_schools' => 0,
                'total_students' => 0,
                'total_majors' => 0,
            ],
            'recent_schools' => [],
            'recent_students' => [],
            'studentsPerMajor' => [],
        ]);
    } catch (Exception $e) {
        \Log::error('Dashboard error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// SuperAdmin Schools (UI) - Temporary bypass auth for testing
Route::get('/schools', function () {
    try {
        // Temporary bypass auth for testing navigation
        // if (!Auth::guard('admin')->check()) {
        //     return redirect('/login');
        // }
        
        return Inertia::render('SuperAdmin/Schools', [
            'title' => 'Manajemen Sekolah',
            'schools' => [
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'total' => 0,
            ],
        ]);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// SuperAdmin Major Recommendations (UI) - Temporary bypass auth for testing
Route::get('/major-recommendations', function () {
    try {
        // Temporary bypass auth for testing navigation
        // if (!Auth::guard('admin')->check()) {
        //     return redirect('/login');
        // }
        
        return Inertia::render('SuperAdmin/MajorRecommendations', [
            'title' => 'Rekomendasi Jurusan',
            'majors' => [],
        ]);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// SuperAdmin Questions (UI) - Temporary bypass auth for testing
Route::get('/questions', function () {
    try {
        // Temporary bypass auth for testing navigation
        // if (!Auth::guard('admin')->check()) {
        //     return redirect('/login');
        // }
        
        return Inertia::render('SuperAdmin/Questions', [
            'title' => 'Bank Soal',
            'questions' => [
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'total' => 0,
            ],
        ]);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// SuperAdmin Results (UI) - Temporary bypass auth for testing
Route::get('/results', function () {
    try {
        // Temporary bypass auth for testing navigation
        // if (!Auth::guard('admin')->check()) {
        //     return redirect('/login');
        // }
        
        return Inertia::render('SuperAdmin/Results', [
            'title' => 'Hasil Tes',
            'results' => [],
        ]);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// SuperAdmin TKA Schedules (UI) - Temporary bypass auth for testing
Route::get('/tka-schedules', function () {
    try {
        // Temporary bypass auth for testing navigation
        // if (!Auth::guard('admin')->check()) {
        //     return redirect('/login');
        // }
        
        return Inertia::render('SuperAdmin/TkaSchedules', [
            'title' => 'Jadwal TKA',
            'schedules' => [],
        ]);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// SuperAdmin Logout
Route::post('/logout', function (Request $request) {
    try {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Root route redirect to dashboard (for testing)
Route::get('/', function () {
    return redirect('/dashboard');
});

// ===========================================
// REDIRECT ROUTES
// ===========================================

// Redirect to dashboard (if authenticated)
Route::get('/home', function () {
    return redirect('/login');
});

// Redirect to admin panel
Route::get('/admin', function () {
    return response()->json(['redirect' => '/dashboard']);
});

// Redirect to superadmin
Route::get('/superadmin', function () {
    return redirect('/dashboard');
});

// Redirect to main app
Route::get('/app', function () {
    return redirect('/dashboard');
});

// Redirect to management
Route::get('/management', function () {
    return redirect('/dashboard');
});

// Redirect to control panel
Route::get('/control', function () {
    return redirect('/dashboard');
});

// Redirect to admin login
Route::get('/admin-login', function () {
    return redirect('/login');
});

// Redirect to signin
Route::get('/signin', function () {
    return redirect('/login');
});

// Redirect to sign-in
Route::get('/sign-in', function () {
    return redirect('/login');
});

// Redirect to auth
Route::get('/auth', function () {
    return redirect('/login');
});

// Redirect to authentication
Route::get('/authentication', function () {
    return redirect('/login');
});

// ===========================================
// ADDITIONAL PAGES
// ===========================================

// About page
Route::get('/about', function () {
    return response()->json([
        'message' => 'About SuperAdmin CampusWay',
        'version' => app()->version(),
        'status' => 'success'
    ]);
});

// Help page
Route::get('/help', function () {
    return Inertia::render('SuperAdmin/Help', [
        'title' => 'Help & Support',
        'version' => app()->version(),
    ]);
});

// Settings page (requires authentication)
Route::get('/settings', function () {
    if (!Auth::guard('admin')->check()) {
        return redirect('/login');
    }
    
    return Inertia::render('SuperAdmin/Settings', [
        'title' => 'Settings',
        'user' => Auth::guard('admin')->user(),
    ]);
});

// Profile page (requires authentication)
Route::get('/profile', function () {
    if (!Auth::guard('admin')->check()) {
        return redirect('/login');
    }
    
    return Inertia::render('SuperAdmin/Profile', [
        'title' => 'Profile',
        'user' => Auth::guard('admin')->user(),
    ]);
});

// Schools management page (requires authentication)
Route::get('/schools', function () {
    if (!Auth::guard('admin')->check()) {
        return redirect('/login');
    }
    
    return Inertia::render('SuperAdmin/Schools', [
        'title' => 'Schools Management',
        'user' => Auth::guard('admin')->user(),
    ]);
});

// Questions management page (requires authentication)
Route::get('/questions', function () {
    if (!Auth::guard('admin')->check()) {
        return redirect('/login');
    }
    
    return Inertia::render('SuperAdmin/Questions', [
        'title' => 'Questions Management',
        'user' => Auth::guard('admin')->user(),
    ]);
});
