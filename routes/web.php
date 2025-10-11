<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

// Export routes (outside web middleware)
Route::get('/csv-export', function() {
    try {
        $majors = \App\Models\MajorRecommendation::all();
        
        $filename = 'major_recommendations_' . date('Y-m-d_H-i-s') . '.csv';
        
        // Create CSV content as string
        $csvContent = '';
        
        // Add BOM for UTF-8
        $csvContent .= "\xEF\xBB\xBF";
        
        // Header
        $csvContent .= '"ID","Nama Jurusan","Kategori","Deskripsi","Mata Pelajaran Wajib","Mata Pelajaran Pilihan","Prospek Karir","Status"' . "\n";

        // Data rows
        foreach ($majors as $major) {
            // Simple array to string conversion
            $requiredStr = '';
            if (is_array($major->required_subjects)) {
                if (!empty($major->required_subjects)) {
                    $names = [];
                    foreach ($major->required_subjects as $item) {
                        if (is_array($item) && isset($item['name'])) {
                            $names[] = $item['name'];
                        } else {
                            $names[] = $item;
                        }
                    }
                    $requiredStr = implode(';', $names);
                }
            } elseif (is_string($major->required_subjects)) {
                $decoded = json_decode($major->required_subjects, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $requiredStr = implode(';', $decoded);
                } else {
                    $requiredStr = $major->required_subjects;
                }
            }
            
            $preferredStr = '';
            if (is_array($major->preferred_subjects)) {
                if (!empty($major->preferred_subjects)) {
                    $names = [];
                    foreach ($major->preferred_subjects as $item) {
                        if (is_array($item) && isset($item['name'])) {
                            $names[] = $item['name'];
                        } else {
                            $names[] = $item;
                        }
                    }
                    $preferredStr = implode(';', $names);
                }
            } elseif (is_string($major->preferred_subjects)) {
                $decoded = json_decode($major->preferred_subjects, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $preferredStr = implode(';', $decoded);
                } else {
                    $preferredStr = $major->preferred_subjects;
                }
            }
            
            $csvContent .= sprintf('"%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $major->id,
                str_replace('"', '""', $major->major_name),
                str_replace('"', '""', $major->category),
                str_replace('"', '""', $major->description),
                str_replace('"', '""', $requiredStr),
                str_replace('"', '""', $preferredStr),
                str_replace('"', '""', $major->career_prospects ?? ''),
                $major->is_active ? 'Aktif' : 'Non-Aktif'
            );
        }

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Gagal mengexport data: ' . $e->getMessage()], 500);
    }
});

// ===========================================
// SUPER ADMIN ROUTES WITH UI RENDERING
// ===========================================

// CSRF token refresh endpoint
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'framework' => 'Laravel', 
        'version' => app()->version(),
        'timestamp' => now()->toISOString()
    ]);
});

// Test DELETE endpoint
Route::middleware(['web'])->group(function () {
    Route::delete('/test-delete', function () {
        return response()->json(['message' => 'DELETE method works!']);
    });
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
        
        // Get real data from database
        $totalSchools = DB::table('schools')->count();
        $totalStudents = DB::table('students')->where('status', 'active')->count();
        $totalMajors = DB::table('major_recommendations')->where('is_active', true)->count();
        
        // Get recent schools
        $recentSchools = DB::table('schools')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Get recent students with school information
        $recentStudents = DB::table('students')
            ->join('schools', 'students.school_id', '=', 'schools.id')
            ->select('students.*', 'schools.name as school_name')
            ->where('students.status', 'active')
            ->orderBy('students.created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Debug logging
        Log::info('Recent students data for dashboard:', $recentStudents->toArray());
            
        // Get students per major
        $studentsPerMajor = DB::table('student_choices')
            ->join('major_recommendations', 'student_choices.major_id', '=', 'major_recommendations.id')
            ->select('major_recommendations.major_name', DB::raw('COUNT(student_choices.student_id) as student_count'))
            ->groupBy('major_recommendations.id', 'major_recommendations.major_name')
            ->orderBy('student_count', 'desc')
            ->limit(10)
            ->get();
        
        Log::info('Rendering SuperAdmin Dashboard with real data');
        return Inertia::render('SuperAdmin/Dashboard', [
            'title' => 'SuperAdmin Dashboard',
            'user' => Auth::guard('admin')->user() ?: (object)['username' => 'Test Admin', 'name' => 'Test Admin'],
            'stats' => [
                'total_schools' => $totalSchools,
                'total_students' => $totalStudents,
                'total_majors' => $totalMajors,
            ],
            'recent_schools' => $recentSchools,
            'recent_students' => $recentStudents,
            'studentsPerMajor' => $studentsPerMajor,
        ]);
    } catch (Exception $e) {
        Log::error('Dashboard error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// SuperAdmin Schools (UI) - Using controller
Route::get('/schools', [App\Http\Controllers\SchoolController::class, 'index']);

// SuperAdmin Schools - Inertia responses (Web)
Route::post('/schools', [App\Http\Controllers\SchoolController::class, 'store']);
Route::get('/schools/{school}', [App\Http\Controllers\SchoolController::class, 'show']);
Route::put('/schools/{school}', [App\Http\Controllers\SchoolController::class, 'update']);
Route::delete('/schools/{school}', [App\Http\Controllers\SchoolController::class, 'destroy']);

Route::post('/schools/import', [App\Http\Controllers\SchoolController::class, 'import']);

// SuperAdmin Students (UI) - Using controller
Route::get('/students', [App\Http\Controllers\StudentController::class, 'index']);

// SuperAdmin Students - Inertia responses (Web)
Route::post('/students', [App\Http\Controllers\StudentController::class, 'store']);
Route::get('/students/{student}', [App\Http\Controllers\StudentController::class, 'show']);
Route::put('/students/{student}', [App\Http\Controllers\StudentController::class, 'update']);
Route::delete('/students/{student}', [App\Http\Controllers\StudentController::class, 'destroy']);

Route::get('/students/export', [App\Http\Controllers\StudentController::class, 'export']);

// SuperAdmin Major Recommendations (UI) - Using controller
Route::get('/major-recommendations', [App\Http\Controllers\MajorRecommendationController::class, 'index']);

// SuperAdmin Major Recommendations - Inertia responses (Web)
Route::post('/major-recommendations', [App\Http\Controllers\MajorRecommendationController::class, 'store']);
Route::get('/major-recommendations/stats', [App\Http\Controllers\MajorRecommendationController::class, 'stats']);
Route::get('/major-recommendations/export', function() {
    try {
        $majors = \App\Models\MajorRecommendation::all();
        
        $filename = 'major_recommendations_' . date('Y-m-d_H-i-s') . '.csv';
        
        // Create CSV content as string
        $csvContent = '';
        
        // Add BOM for UTF-8
        $csvContent .= "\xEF\xBB\xBF";
        
        // Header
        $csvContent .= '"ID","Nama Jurusan","Kategori","Deskripsi","Mata Pelajaran Wajib","Mata Pelajaran Pilihan","Prospek Karir","Status"' . "\n";

        // Data rows
        foreach ($majors as $major) {
            // Simple array to string conversion
            $requiredStr = '';
            if (is_array($major->required_subjects)) {
                if (!empty($major->required_subjects)) {
                    $names = [];
                    foreach ($major->required_subjects as $item) {
                        if (is_array($item) && isset($item['name'])) {
                            $names[] = $item['name'];
                        } else {
                            $names[] = $item;
                        }
                    }
                    $requiredStr = implode(';', $names);
                }
            } elseif (is_string($major->required_subjects)) {
                $decoded = json_decode($major->required_subjects, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $requiredStr = implode(';', $decoded);
                } else {
                    $requiredStr = $major->required_subjects;
                }
            }
            
            $preferredStr = '';
            if (is_array($major->preferred_subjects)) {
                if (!empty($major->preferred_subjects)) {
                    $names = [];
                    foreach ($major->preferred_subjects as $item) {
                        if (is_array($item) && isset($item['name'])) {
                            $names[] = $item['name'];
                        } else {
                            $names[] = $item;
                        }
                    }
                    $preferredStr = implode(';', $names);
                }
            } elseif (is_string($major->preferred_subjects)) {
                $decoded = json_decode($major->preferred_subjects, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $preferredStr = implode(';', $decoded);
                } else {
                    $preferredStr = $major->preferred_subjects;
                }
            }
            
            $csvContent .= sprintf('"%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $major->id,
                str_replace('"', '""', $major->major_name),
                str_replace('"', '""', $major->category),
                str_replace('"', '""', $major->description),
                str_replace('"', '""', $requiredStr),
                str_replace('"', '""', $preferredStr),
                str_replace('"', '""', $major->career_prospects ?? ''),
                $major->is_active ? 'Aktif' : 'Non-Aktif'
            );
        }

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Gagal mengexport data: ' . $e->getMessage()], 500);
    }
})->withoutMiddleware([\App\Http\Middleware\HandleInertiaRequests::class]);
Route::get('/major-recommendations/{majorRecommendation}', [App\Http\Controllers\MajorRecommendationController::class, 'show']);
Route::put('/major-recommendations/{majorRecommendation}', [App\Http\Controllers\MajorRecommendationController::class, 'update']);
Route::delete('/major-recommendations/{majorRecommendation}', [App\Http\Controllers\MajorRecommendationController::class, 'destroy']);
Route::patch('/major-recommendations/{majorRecommendation}/toggle', [App\Http\Controllers\MajorRecommendationController::class, 'toggle']);

// Alias route for /jurusan
Route::get('/jurusan', [App\Http\Controllers\MajorRecommendationController::class, 'index']);

// SuperAdmin Questions (UI) - Using controller
Route::get('/questions', [App\Http\Controllers\QuestionController::class, 'index']);

// SuperAdmin Questions - Inertia responses (Web)
Route::post('/questions', [App\Http\Controllers\QuestionController::class, 'store']);
Route::get('/questions/{question}', [App\Http\Controllers\QuestionController::class, 'show']);
Route::put('/questions/{question}', [App\Http\Controllers\QuestionController::class, 'update']);
Route::delete('/questions/{question}', [App\Http\Controllers\QuestionController::class, 'destroy']);

// SuperAdmin Results (UI) - Using controller
Route::get('/results', [App\Http\Controllers\ResultController::class, 'index']);
Route::get('/results/{result}', [App\Http\Controllers\ResultController::class, 'show']);
Route::delete('/results/{result}', [App\Http\Controllers\ResultController::class, 'destroy']);
Route::get('/results/export', [App\Http\Controllers\ResultController::class, 'export']);

// SuperAdmin TKA Schedules (UI) - Using controller
Route::get('/admin/tka-schedules', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'index']);

// SuperAdmin TKA Schedules - Inertia responses (Web)
Route::post('/tka-schedules', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'store']);
Route::get('/tka-schedules/{tkaSchedule}', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'show']);
Route::put('/tka-schedules/{tkaSchedule}', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'update']);
Route::delete('/tka-schedules/{tkaSchedule}', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'destroy']);
Route::patch('/tka-schedules/{tkaSchedule}/toggle', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'cancel']);

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
