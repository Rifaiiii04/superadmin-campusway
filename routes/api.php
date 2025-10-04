<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentWebController;
use App\Http\Controllers\SchoolAuthController;
use App\Http\Controllers\SchoolDashboardController;
use App\Http\Controllers\OptimizedApiController;
use App\Http\Controllers\SchoolLevelMajorController;
use App\Http\Controllers\TkaScheduleController;
use App\Http\Controllers\StudentSubjectController;
use App\Http\Controllers\ApiController;

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

// Public API Routes for Next.js integration
Route::prefix('public')->middleware('cors')->group(function () {
    // Health check
    Route::get('/health', [ApiController::class, 'health']);
    
    // Schools
    Route::get('/schools', [ApiController::class, 'getSchools']);
    
    // Questions
    Route::get('/questions', [ApiController::class, 'getQuestions']);
    
    // Results
    Route::get('/results', [ApiController::class, 'getResults']);
    
    // Majors
    Route::get('/majors', [ApiController::class, 'getMajors']);
    
    // School statistics
    Route::get('/school-stats', [ApiController::class, 'getSchoolStats']);
    
    // Cache management
    Route::post('/clear-cache', [ApiController::class, 'clearCache']);
});

// Optimized API Routes for better performance
Route::prefix('optimized')->group(function () {
    // Health check
    Route::get('/health', [OptimizedApiController::class, 'health']);

    // Cached endpoints
    Route::get('/majors', [OptimizedApiController::class, 'getMajors']);
    Route::get('/majors/{id}', [OptimizedApiController::class, 'getMajorDetails']);
    Route::get('/schools', [OptimizedApiController::class, 'getSchools']);

    // Optimized student endpoints
    Route::post('/login', [OptimizedApiController::class, 'login']);
    Route::get('/student-choice/{studentId}', [OptimizedApiController::class, 'getStudentChoice']);
    Route::get('/major-status/{studentId}', [OptimizedApiController::class, 'checkMajorStatus']);

    // Cache management
    Route::post('/clear-cache', [OptimizedApiController::class, 'clearCache']);
});

// Performance monitoring routes
Route::prefix('performance')->group(function () {
    Route::get('/metrics', [App\Http\Controllers\PerformanceController::class, 'getMetrics']);
    Route::post('/optimize', [App\Http\Controllers\PerformanceController::class, 'optimize']);
    Route::get('/health', [App\Http\Controllers\PerformanceController::class, 'health']);
});



// Student Web API Routes (for TKA Web)
Route::prefix('web')->middleware('cors')->group(function () {
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
    
    // Check student's major status
    Route::get('/major-status/{studentId}', [StudentWebController::class, 'checkMajorStatus']);
    
    // Change student's major choice
    Route::post('/change-major', [StudentWebController::class, 'changeMajor']);
    
    // Get student profile
    Route::get('/student-profile/{studentId}', [StudentWebController::class, 'getStudentProfile']);
    
    // Test endpoint untuk debugging
    Route::post('/test-choose-major', [StudentWebController::class, 'testChooseMajor']);
    
    // TKA Schedules for students
    Route::get('/tka-schedules', [TkaScheduleController::class, 'index']);
    Route::get('/tka-schedules/upcoming', [TkaScheduleController::class, 'upcoming']);
    
    // Student subjects based on major choice
    Route::get('/student-subjects/{studentId}', [StudentSubjectController::class, 'getStudentSubjects']);
    Route::get('/subjects-for-major', [StudentSubjectController::class, 'getSubjectsForMajor']);
});

// School Dashboard API Routes
Route::prefix('school')->middleware('cors')->group(function () {
    // Authentication routes (tidak perlu middleware)
    Route::post('/login', [SchoolAuthController::class, 'login']);
    Route::post('/logout', [SchoolAuthController::class, 'logout']);
    
    // Protected routes (perlu authentication)
    Route::middleware('school.auth')->group(function () {
        // Profile sekolah
        Route::get('/profile', [SchoolAuthController::class, 'profile']);
        Route::post('/change-password', [SchoolAuthController::class, 'updatePassword']);
        
        // Dashboard overview
        Route::get('/dashboard', [SchoolDashboardController::class, 'dashboard']);
        
        // Data siswa
        Route::get('/students', [SchoolDashboardController::class, 'students']);
        Route::post('/students', [SchoolDashboardController::class, 'addStudent']);
        Route::get('/students/{studentId}', [SchoolDashboardController::class, 'studentDetail']);
        Route::put('/students/{studentId}', [SchoolDashboardController::class, 'updateStudent']);
        Route::delete('/students/{studentId}', [SchoolDashboardController::class, 'deleteStudent']);
        Route::get('/students-without-choice', [SchoolDashboardController::class, 'studentsWithoutChoice']);
        
        // Statistik jurusan
        Route::get('/major-statistics', [SchoolDashboardController::class, 'majorStatistics']);
        
        // Export data siswa
        Route::get('/export-students', [SchoolDashboardController::class, 'exportStudents']);
        
        // Import data siswa
        Route::post('/import-students', [SchoolDashboardController::class, 'importStudents']);
        
        // Download template import
        Route::get('/import-template', [SchoolDashboardController::class, 'downloadImportTemplate']);
        
        // Get import rules
        Route::get('/import-rules', [SchoolDashboardController::class, 'getImportRules']);
        
        // Get classes list
        Route::get('/classes', [SchoolDashboardController::class, 'getClasses']);
    });
});

// Super Admin API Routes (for testing)
Route::prefix('super-admin-api')->group(function () {
    Route::put('/schools/{school}', [App\Http\Controllers\SuperAdminController::class, 'updateSchool']);
    Route::post('/schools', [App\Http\Controllers\SuperAdminController::class, 'storeSchool']);
    Route::delete('/schools/{school}', [App\Http\Controllers\SuperAdminController::class, 'deleteSchool']);
});

// School Level Major Recommendations API Routes
Route::prefix('school-level')->group(function () {
    // Get major recommendations by school level
    Route::get('/majors', [SchoolLevelMajorController::class, 'getMajorsBySchoolLevel']);
    
    // Get subjects by school level
    Route::get('/subjects', [SchoolLevelMajorController::class, 'getSubjectsBySchoolLevel']);
    
    // Get school level statistics
    Route::get('/stats', [SchoolLevelMajorController::class, 'getSchoolLevelStats']);
});

// TKA Schedule API Routes
Route::prefix('tka-schedules')->group(function () {
    // Public routes (for teacher and student dashboards)
    Route::get('/', [TkaScheduleController::class, 'index']);
    Route::get('/upcoming', [TkaScheduleController::class, 'upcoming']);
    
    // Admin routes (for super admin dashboard)
    Route::post('/', [TkaScheduleController::class, 'store']);
    Route::get('/{id}', [TkaScheduleController::class, 'show']);
    Route::put('/{id}', [TkaScheduleController::class, 'update']);
    Route::delete('/{id}', [TkaScheduleController::class, 'destroy']);
    Route::post('/{id}/cancel', [TkaScheduleController::class, 'cancel']);
});
