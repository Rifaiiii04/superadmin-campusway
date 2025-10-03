<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SchoolAuthController;
use App\Http\Controllers\SchoolDashboardController;
use App\Http\Controllers\SuperAdmin\TkaScheduleController;

// ===========================================
// PUBLIC ROUTES - FIXED
// ===========================================

// Redirect root to login
Route::get('/', function () {
    return Inertia::render('SuperAdmin/Login');
});

// Super Admin Login (Public) - HANYA INI
Route::get('/login', [SuperAdminController::class, 'showLogin'])->name('login');
Route::post('/login', [SuperAdminController::class, 'login']);

// Logout
Route::post('/logout', function () {
    Auth::guard('admin')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// ===========================================
// PROTECTED SUPER ADMIN ROUTES - FIXED
// ===========================================
Route::middleware(['auth:admin'])->group(function () {
    // Dashboard - HANYA SATU ROUTE
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    
    // Schools Management
    Route::get('/schools', [SuperAdminController::class, 'schools'])->name('schools');
    Route::get('/schools/{school}', [SuperAdminController::class, 'schoolDetail'])->name('schools.detail');
    Route::post('/schools', [SuperAdminController::class, 'storeSchool'])->name('schools.store');
    Route::put('/schools/{school}', [SuperAdminController::class, 'updateSchool'])->name('schools.update');
    Route::patch('/schools/{school}', [SuperAdminController::class, 'updateSchool'])->name('schools.patch');
    Route::delete('/schools/{school}', [SuperAdminController::class, 'deleteSchool'])->name('schools.delete');
    
    // Other Modules
    Route::get('/major-recommendations', [SuperAdminController::class, 'majorRecommendations'])->name('major-recommendations');
    Route::get('/questions', [SuperAdminController::class, 'questions'])->name('questions');
    Route::get('/results', [SuperAdminController::class, 'results'])->name('results');
    Route::get('/tka-schedules', [TkaScheduleController::class, 'index'])->name('tka-schedules');
});

// ===========================================
// SCHOOL ROUTES
// ===========================================
Route::prefix('school')->group(function () {
    Route::get('/login', [SchoolAuthController::class, 'showLogin'])->name('school.login');
    Route::post('/login', [SchoolAuthController::class, 'login']);
    Route::get('/dashboard', [SchoolDashboardController::class, 'index'])->name('school.dashboard');
});

// ===========================================
// TEST ROUTE (Optional)
// ===========================================
Route::get('/test', function() {
    return response()->json(['status' => 'OK', 'message' => 'Routes working']);
});
