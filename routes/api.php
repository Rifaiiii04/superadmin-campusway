<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\SchoolAuthController;
use App\Http\Controllers\StudentWebController;
use App\Http\Controllers\SchoolDashboardController;
use App\Http\Controllers\TkaScheduleController;

// ===========================================
// STUDENT WEB API ROUTES (Public - No Auth)
// ===========================================
Route::prefix('web')->group(function () {
    // Public endpoints
    Route::get('/schools', [StudentWebController::class, 'getSchools']);
    Route::get('/questions', [ApiController::class, 'getQuestions']);
    Route::get('/majors', [StudentWebController::class, 'getMajors']);
    Route::get('/majors/{id}', [StudentWebController::class, 'getMajorDetails']);
    Route::get('/health', [ApiController::class, 'healthCheck']);
    
    // Student authentication
    Route::post('/register-student', [StudentWebController::class, 'register']);
    Route::post('/login', [StudentWebController::class, 'login']);
    
    // Student major selection (no auth required for simplicity)
    Route::post('/choose-major', [StudentWebController::class, 'chooseMajor']);
    Route::post('/change-major', [StudentWebController::class, 'changeMajor']);
    Route::get('/student-choice/{studentId}', [StudentWebController::class, 'getStudentChoice']);
    Route::get('/major-status/{studentId}', [StudentWebController::class, 'checkMajorStatus']);
    Route::get('/student-profile/{studentId}', [StudentWebController::class, 'getStudentProfile']);
    
    // TKA Schedules for students
    Route::get('/tka-schedules', [TkaScheduleController::class, 'index']);
    Route::get('/tka-schedules/upcoming', [TkaScheduleController::class, 'upcoming']);
});

// ===========================================
// SCHOOL DASHBOARD API ROUTES (With Auth)
// ===========================================
Route::prefix('school')->group(function () {
    // School authentication
    Route::post('/login', [SchoolAuthController::class, 'login']);
    Route::post('/logout', [SchoolAuthController::class, 'logout'])->middleware('school.auth');
    
    // Protected routes (require authentication)
    Route::middleware('school.auth')->group(function () {
        Route::get('/profile', [SchoolAuthController::class, 'profile']);
        Route::get('/dashboard', [SchoolDashboardController::class, 'index']);
        Route::get('/students', [SchoolDashboardController::class, 'students']);
        Route::get('/students/{id}', [SchoolDashboardController::class, 'studentDetail']);
        Route::get('/major-statistics', [SchoolDashboardController::class, 'majorStatistics']);
        Route::get('/export-students', [SchoolDashboardController::class, 'exportStudents']);
        Route::get('/students-without-choice', [SchoolDashboardController::class, 'studentsWithoutChoice']);
        
        // Student management
        Route::post('/students', [SchoolDashboardController::class, 'addStudent']);
        Route::put('/students/{id}', [SchoolDashboardController::class, 'updateStudent']);
        Route::delete('/students/{id}', [SchoolDashboardController::class, 'deleteStudent']);
        
        // Import students
        Route::post('/import-students', [SchoolDashboardController::class, 'importStudents']);
        Route::get('/import-template', [SchoolDashboardController::class, 'downloadTemplate']);
        Route::get('/import-rules', [SchoolDashboardController::class, 'importRules']);
        
        // Classes
        Route::get('/classes', [SchoolDashboardController::class, 'getClasses']);
        
        // TKA Schedules
        Route::get('/tka-schedules', [TkaScheduleController::class, 'index']);
        Route::get('/tka-schedules/upcoming', [TkaScheduleController::class, 'upcoming']);
    });
});

// ===========================================
// PUBLIC API ROUTES (SuperAdmin Integration)
// ===========================================
Route::prefix('public')->group(function () {
    Route::get('/schools', [ApiController::class, 'getSchools']);
    Route::get('/questions', [ApiController::class, 'getQuestions']);
    Route::get('/majors', [ApiController::class, 'getMajors']);
    Route::get('/health', [ApiController::class, 'healthCheck']);
    
    // Student web API routes
    Route::post('/login', [StudentWebController::class, 'login']);
    Route::post('/register-student', [StudentWebController::class, 'registerStudent']);
    Route::get('/schools', [StudentWebController::class, 'getSchools']);
    Route::get('/majors', [StudentWebController::class, 'getMajors']);
    Route::get('/majors/{id}', [StudentWebController::class, 'getMajorDetails']);
    Route::get('/major-status/{studentId}', [StudentWebController::class, 'checkMajorStatus']);
    Route::get('/student-choice/{studentId}', [StudentWebController::class, 'getStudentChoice']);
    Route::post('/choose-major', [StudentWebController::class, 'chooseMajor']);
    Route::post('/change-major', [StudentWebController::class, 'changeMajor']);
    Route::get('/student-profile/{studentId}', [StudentWebController::class, 'getStudentProfile']);
    Route::get('/student-subjects/{studentId}', [StudentWebController::class, 'getStudentSubjects']);
    Route::get('/subjects-for-major', [StudentWebController::class, 'getSubjectsForMajor']);
    Route::get('/tka-schedules', [StudentWebController::class, 'getTkaSchedules']);
    Route::get('/tka-schedules/upcoming', [StudentWebController::class, 'getUpcomingTkaSchedules']);
});

// School authentication routes (no middleware required for login)
Route::prefix('school')->group(function () {
    Route::post('/login', [SchoolAuthController::class, 'login']);
    Route::post('/logout', [SchoolAuthController::class, 'logout']);
    Route::get('/profile', [SchoolAuthController::class, 'profile']);
    
    // Test route without authentication
    Route::get('/test-dashboard', function () {
        return response()->json([
            'success' => true,
            'message' => 'CORS test successful',
            'data' => [
                'cors_working' => true,
                'timestamp' => now()
            ]
        ]);
    });
    
    // Test dashboard without authentication
    Route::get('/test-dashboard-data', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'school' => [
                    'id' => 8,
                    'name' => 'SMK Negeri 1 Karawang',
                    'npsn' => '44556677',
                    'address' => 'Test Address',
                    'phone' => '021-123456',
                    'email' => 'test@school.com',
                ],
                'statistics' => [
                    'total_students' => 100,
                    'students_with_choices' => 50,
                    'students_without_choices' => 50,
                ],
                'recent_students' => []
            ]
        ]);
    });
    
    // Protected school routes (with authentication) - REMOVED DUPLICATE ROUTES
    // These routes are already defined above with proper school.auth middleware
});

// ===========================================
// EXPORT ROUTES (No middleware)
// ===========================================
Route::get('/major-recommendations/export', [App\Http\Controllers\MajorRecommendationController::class, 'export']);

// TKA Schedules routes (public access)
Route::get('/tka-schedules', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'index']);
Route::get('/tka-schedules/upcoming', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'upcoming']);
