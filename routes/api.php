<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentWebController;
use App\Http\Controllers\SchoolAuthController;
use App\Http\Controllers\SchoolDashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



// Student Web API Routes (for TKA Web)
Route::prefix('web')->group(function () {
    // Student registration
    Route::post('/register-student', [StudentWebController::class, 'register']);
    
    // Student login
    Route::post('/login', [StudentWebController::class, 'login']);
    
    // Get available schools
    Route::get('/schools', [StudentWebController::class, 'getSchools']);
    
    // Get all active majors
    Route::get('/majors', [StudentWebController::class, 'getMajors']);
    
    // Get major details
    Route::get('/majors/{id}', [StudentWebController::class, 'getMajorDetails']);
    
    // Student choose major
    Route::post('/choose-major', [StudentWebController::class, 'chooseMajor']);
    
    // Get student's chosen major
    Route::get('/student-choice/{studentId}', [StudentWebController::class, 'getStudentChoice']);
    
    // Change student's major choice
    Route::post('/change-major', [StudentWebController::class, 'changeMajor']);
    
    // Get student profile
    Route::get('/student-profile/{studentId}', [StudentWebController::class, 'getStudentProfile']);
    
    // Test endpoint untuk debugging
    Route::post('/test-choose-major', [StudentWebController::class, 'testChooseMajor']);
});

// School Dashboard API Routes
Route::prefix('school')->group(function () {
    // Authentication routes (tidak perlu middleware)
    Route::post('/login', [SchoolAuthController::class, 'login']);
    Route::post('/logout', [SchoolAuthController::class, 'logout']);
    
    // Protected routes (perlu authentication)
    Route::middleware('school.auth')->group(function () {
        // Profile sekolah
        Route::get('/profile', [SchoolAuthController::class, 'profile']);
        
        // Dashboard overview
        Route::get('/dashboard', [SchoolDashboardController::class, 'dashboard']);
        
        // Data siswa
        Route::get('/students', [SchoolDashboardController::class, 'students']);
        Route::get('/students/{studentId}', [SchoolDashboardController::class, 'studentDetail']);
        Route::get('/students-without-choice', [SchoolDashboardController::class, 'studentsWithoutChoice']);
        
        // Statistik jurusan
        Route::get('/major-statistics', [SchoolDashboardController::class, 'majorStatistics']);
    });
});
