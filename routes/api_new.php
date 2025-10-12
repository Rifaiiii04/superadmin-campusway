<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\StudentWebController;
use App\Http\Controllers\SchoolAuthController;
use App\Http\Controllers\SchoolDashboardController;
use App\Http\Controllers\TkaScheduleController;

// ===========================================
// STUDENT WEB API ROUTES (Public - No Auth)
// ===========================================
Route::prefix("web")->group(function () {
    // Public endpoints
    Route::get("/schools", [StudentWebController::class, "getSchools"]);
    Route::get("/questions", [ApiController::class, "getQuestions"]);
    Route::get("/majors", [StudentWebController::class, "getMajors"]);
    Route::get("/majors/{id}", [StudentWebController::class, "getMajorDetails"]);
    Route::get("/health", [ApiController::class, "healthCheck"]);
    
    // Student authentication
    Route::post("/register-student", [StudentWebController::class, "register"]);
    Route::post("/login", [StudentWebController::class, "login"]);
    
    // Student major selection (no auth required for simplicity)
    Route::post("/choose-major", [StudentWebController::class, "chooseMajor"]);
    Route::post("/change-major", [StudentWebController::class, "changeMajor"]);
    Route::get("/student-choice/{studentId}", [StudentWebController::class, "getStudentChoice"]);
    Route::get("/major-status/{studentId}", [StudentWebController::class, "checkMajorStatus"]);
    Route::get("/student-profile/{studentId}", [StudentWebController::class, "getStudentProfile"]);
    
    // TKA Schedules for students
    Route::get("/tka-schedules", [TkaScheduleController::class, "index"]);
    Route::get("/tka-schedules/upcoming", [TkaScheduleController::class, "upcoming"]);
});

// ===========================================
// SCHOOL DASHBOARD API ROUTES (With Auth)
// ===========================================
Route::prefix("school")->group(function () {
    // School authentication
    Route::post("/login", [SchoolAuthController::class, "login"]);
    Route::post("/logout", [SchoolAuthController::class, "logout"])->middleware("auth:sanctum");
    
    // Protected routes (require authentication)
    Route::middleware("auth:sanctum")->group(function () {
        Route::get("/profile", [SchoolAuthController::class, "profile"]);
        Route::get("/dashboard", [SchoolDashboardController::class, "index"]);
        Route::get("/students", [SchoolDashboardController::class, "students"]);
        Route::get("/students/{id}", [SchoolDashboardController::class, "studentDetail"]);
        Route::get("/major-statistics", [SchoolDashboardController::class, "majorStatistics"]);
        Route::get("/export-students", [SchoolDashboardController::class, "exportStudents"]);
        Route::get("/students-without-choice", [SchoolDashboardController::class, "studentsWithoutChoice"]);
        
        // Student management
        Route::post("/students", [SchoolDashboardController::class, "storeStudent"]);
        Route::put("/students/{id}", [SchoolDashboardController::class, "updateStudent"]);
        Route::delete("/students/{id}", [SchoolDashboardController::class, "deleteStudent"]);
        
        // Import students
        Route::post("/import-students", [SchoolDashboardController::class, "importStudents"]);
        Route::get("/import-template", [SchoolDashboardController::class, "downloadTemplate"]);
        Route::get("/import-rules", [SchoolDashboardController::class, "importRules"]);
        
        // Classes
        Route::get("/classes", [SchoolDashboardController::class, "classes"]);
        
        // TKA Schedules
        Route::get("/tka-schedules", [TkaScheduleController::class, "index"]);
        Route::get("/tka-schedules/upcoming", [TkaScheduleController::class, "upcoming"]);
    });
});

// ===========================================
// PUBLIC API ROUTES (SuperAdmin Integration)
// ===========================================
Route::prefix("public")->group(function () {
    Route::get("/schools", [ApiController::class, "getSchools"]);
    Route::get("/questions", [ApiController::class, "getQuestions"]);
    Route::get("/majors", [ApiController::class, "getMajors"]);
    Route::get("/health", [ApiController::class, "healthCheck"]);
});

// ===========================================
// EXPORT ROUTES (No middleware)
// ===========================================
Route::get("/major-recommendations/export", [App\Http\Controllers\MajorRecommendationController::class, "export"]);

// ===========================================
// TKA SCHEDULES PUBLIC ROUTES
// ===========================================
Route::get("/tka-schedules", [TkaScheduleController::class, "index"]);
Route::get("/tka-schedules/upcoming", [TkaScheduleController::class, "upcoming"]);
