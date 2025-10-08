<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
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
        Log::info('Dashboard access attempt, admin check: ' . (Auth::guard('admin')->check() ? 'true' : 'false'));
        
        // Temporary bypass auth for testing navigation
        // if (!Auth::guard('admin')->check()) {
        //     Log::info('Admin not authenticated, redirecting to login');
        //     return redirect('/login');
        // }
        
        Log::info('Rendering SuperAdmin Dashboard');
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
        Log::error('Dashboard error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// SuperAdmin Schools (UI) - Using controller
Route::get('/schools', [App\Http\Controllers\SchoolController::class, 'index']);

// SuperAdmin Schools (API) - JSON responses
Route::middleware(['web'])->withoutMiddleware([\App\Http\Middleware\HandleInertiaRequests::class])->group(function () {
    Route::post('/schools', [App\Http\Controllers\SchoolController::class, 'store']);
    Route::get('/schools/{school}', [App\Http\Controllers\SchoolController::class, 'show']);
    Route::put('/schools/{school}', [App\Http\Controllers\SchoolController::class, 'update']);
    Route::delete('/schools/{school}', [App\Http\Controllers\SchoolController::class, 'destroy']);
});

Route::post('/schools/import', [App\Http\Controllers\SchoolController::class, 'import']);

// SuperAdmin Students (UI) - Using controller
Route::get('/students', [App\Http\Controllers\StudentController::class, 'index']);

// SuperAdmin Students (API) - JSON responses
Route::middleware(['web'])->withoutMiddleware([\App\Http\Middleware\HandleInertiaRequests::class])->group(function () {
    Route::post('/students', [App\Http\Controllers\StudentController::class, 'store']);
    Route::get('/students/{student}', [App\Http\Controllers\StudentController::class, 'show']);
    Route::put('/students/{student}', [App\Http\Controllers\StudentController::class, 'update']);
    Route::delete('/students/{student}', [App\Http\Controllers\StudentController::class, 'destroy']);
});

Route::get('/students/export', [App\Http\Controllers\StudentController::class, 'export']);

// SuperAdmin Major Recommendations (UI) - Using controller
Route::get('/major-recommendations', [App\Http\Controllers\MajorRecommendationController::class, 'index']);

// SuperAdmin Major Recommendations (API) - JSON responses
Route::middleware(['web'])->withoutMiddleware([\App\Http\Middleware\HandleInertiaRequests::class])->group(function () {
    Route::post('/major-recommendations', [App\Http\Controllers\MajorRecommendationController::class, 'store']);
    Route::get('/major-recommendations/{majorRecommendation}', [App\Http\Controllers\MajorRecommendationController::class, 'show']);
    Route::put('/major-recommendations/{majorRecommendation}', [App\Http\Controllers\MajorRecommendationController::class, 'update']);
    Route::delete('/major-recommendations/{majorRecommendation}', [App\Http\Controllers\MajorRecommendationController::class, 'destroy']);
    Route::patch('/major-recommendations/{majorRecommendation}/toggle', [App\Http\Controllers\MajorRecommendationController::class, 'toggle']);
});

Route::get('/major-recommendations/export', [App\Http\Controllers\MajorRecommendationController::class, 'export']);
Route::get('/major-recommendations/stats', [App\Http\Controllers\MajorRecommendationController::class, 'stats']);

// Alias route for /jurusan
Route::get('/jurusan', [App\Http\Controllers\MajorRecommendationController::class, 'index']);

// SuperAdmin Questions (UI) - Using controller
Route::get('/questions', [App\Http\Controllers\QuestionController::class, 'index']);

// SuperAdmin Questions (API) - JSON responses
Route::middleware(['web'])->withoutMiddleware([\App\Http\Middleware\HandleInertiaRequests::class])->group(function () {
    Route::post('/questions', [App\Http\Controllers\QuestionController::class, 'store']);
    Route::get('/questions/{question}', [App\Http\Controllers\QuestionController::class, 'show']);
    Route::put('/questions/{question}', [App\Http\Controllers\QuestionController::class, 'update']);
    Route::delete('/questions/{question}', [App\Http\Controllers\QuestionController::class, 'destroy']);
});

// SuperAdmin Results (UI) - Using controller
Route::get('/results', [App\Http\Controllers\ResultController::class, 'index']);
Route::get('/results/{result}', [App\Http\Controllers\ResultController::class, 'show']);
Route::delete('/results/{result}', [App\Http\Controllers\ResultController::class, 'destroy']);
Route::get('/results/export', [App\Http\Controllers\ResultController::class, 'export']);

// SuperAdmin TKA Schedules (UI) - Using controller
Route::get('/tka-schedules', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'index']);

// SuperAdmin TKA Schedules (API) - JSON responses
Route::middleware(['web'])->withoutMiddleware([\App\Http\Middleware\HandleInertiaRequests::class])->group(function () {
    Route::post('/tka-schedules', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'store']);
    Route::get('/tka-schedules/{tkaSchedule}', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'show']);
    Route::put('/tka-schedules/{tkaSchedule}', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'update']);
    Route::delete('/tka-schedules/{tkaSchedule}', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'destroy']);
    Route::patch('/tka-schedules/{tkaSchedule}/toggle', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'cancel']);
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
    return redirect('/dashboard');
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
    return Inertia::render('SuperAdmin/About', [
        'title' => 'About SuperAdmin CampusWay',
        'version' => app()->version(),
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

// Schools management page - REMOVED (duplicate route)

// Questions management page - REMOVED (duplicate route)
Route::get("/major-recommendations/stats", [App\Http\Controllers\MajorRecommendationController::class, "stats"]);
