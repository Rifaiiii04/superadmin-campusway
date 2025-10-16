<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\SchoolAuthController;
use App\Http\Controllers\StudentWebController;

// Public API routes (no authentication required)
Route::prefix('web')->group(function () {
    Route::get('/schools', [ApiController::class, 'getSchools']);
    Route::get('/questions', [ApiController::class, 'getQuestions']);
    Route::get('/majors', [ApiController::class, 'getMajors']);
    Route::get('/health', [ApiController::class, 'healthCheck']);
    
    // Student web API routes
    Route::post('/login', [StudentWebController::class, 'login']);
    Route::post('/register-student', [StudentWebController::class, 'registerStudent']);
    Route::get('/schools', [StudentWebController::class, 'getSchools']);
    Route::get('/majors', [StudentWebController::class, 'getMajors']);
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
    
    // Protected school routes (with authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/schools', [ApiController::class, 'getSchools']);
        Route::get('/questions', [ApiController::class, 'getQuestions']);
        Route::get('/majors', [ApiController::class, 'getMajors']);
        Route::get('/dashboard', [SchoolAuthController::class, 'dashboard']);
        Route::get('/students', [SchoolAuthController::class, 'getStudents']);
        Route::get('/students/{id}', [SchoolAuthController::class, 'getStudentDetail']);
        Route::post('/students', [SchoolAuthController::class, 'addStudent']);
        Route::put('/students/{id}', [SchoolAuthController::class, 'updateStudent']);
        Route::delete('/students/{id}', [SchoolAuthController::class, 'deleteStudent']);
        Route::get('/students-without-choice', [SchoolAuthController::class, 'getStudentsWithoutChoice']);
        Route::get('/major-statistics', [SchoolAuthController::class, 'getMajorStatistics']);
        Route::get('/export-students', [SchoolAuthController::class, 'exportStudents']);
        Route::post('/import-students', [SchoolAuthController::class, 'importStudents']);
        Route::get('/import-template', [SchoolAuthController::class, 'downloadImportTemplate']);
        Route::get('/import-rules', [SchoolAuthController::class, 'getImportRules']);
        Route::get('/classes', [SchoolAuthController::class, 'getClasses']);
    });
});

// Export routes (no middleware)
Route::get('/major-recommendations/export', [App\Http\Controllers\MajorRecommendationController::class, 'export']);

// TKA Schedules routes (public access)
Route::get('/tka-schedules', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'index']);
Route::get('/tka-schedules/upcoming', [App\Http\Controllers\SuperAdmin\TkaScheduleController::class, 'upcoming']);
