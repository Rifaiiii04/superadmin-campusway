<?php

use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SchoolAuthController;
use App\Http\Controllers\SchoolDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public routes - Redirect to TKA Super Admin Login
Route::get('/', function () {
    return redirect('/super-admin/login');
});

// Laravel Auth routes (for admin)
Route::get('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);

// Dashboard route
Route::get('/dashboard', function () {
    return redirect('/super-admin/dashboard');
})->middleware('auth:admin')->name('dashboard');


// School Auth Routes
Route::prefix('school')->group(function () {
    Route::get('/login', [SchoolAuthController::class, 'showLogin'])->name('school.login');
    Route::post('/login', [SchoolAuthController::class, 'login']);
    Route::get('/dashboard', [SchoolDashboardController::class, 'index'])->name('school.dashboard');
});

// Super Admin Routes
Route::prefix('super-admin')->group(function () {
    // Public login routes (no auth required)
    Route::get('/login', [SuperAdminController::class, 'showLogin'])->name('super-admin.login');
    Route::post('/login', [SuperAdminController::class, 'login']);
    
    // Protected routes (require auth)
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/', function () {
            return redirect('/super-admin/dashboard');
        })->name('super-admin');
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('super-admin.dashboard');
        Route::get('/schools', [SuperAdminController::class, 'schools'])->name('super-admin.schools');
        Route::get('/major-recommendations', [SuperAdminController::class, 'majorRecommendations'])->name('super-admin.major-recommendations');
        Route::get('/questions', [SuperAdminController::class, 'questions'])->name('super-admin.questions');
        Route::get('/results', [SuperAdminController::class, 'results'])->name('super-admin.results');
        Route::get('/tka-schedules', [SuperAdminController::class, 'tkaSchedules'])->name('super-admin.tka-schedules');
    });
});

// Test endpoint untuk debug major recommendations
Route::get('/test-majors', function() {
    $majors = \App\Models\MajorRecommendation::all();
    return response()->json([
        'total' => $majors->count(),
        'ilmu_alam' => $majors->where('category', 'Ilmu Alam')->count(),
        'ilmu_sosial' => $majors->where('category', 'Ilmu Sosial')->count(),
        'humaniora' => $majors->where('category', 'Humaniora')->count(),
        'ilmu_formal' => $majors->where('category', 'Ilmu Formal')->count(),
        'ilmu_terapan' => $majors->where('category', 'Ilmu Terapan')->count(),
        'ilmu_kesehatan' => $majors->where('category', 'Ilmu Kesehatan')->count(),
        'ilmu_lingkungan' => $majors->where('category', 'Ilmu Lingkungan')->count(),
        'ilmu_teknologi' => $majors->where('category', 'Ilmu Teknologi')->count(),
        'sample_ilmu_alam' => $majors->where('category', 'Ilmu Alam')->take(3)->pluck('major_name')->toArray()
    ]);
});