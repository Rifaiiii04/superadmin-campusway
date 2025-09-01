<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentApiController;

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

// Student Testing API Routes
Route::prefix('student')->group(function () {
    // Test endpoint untuk debugging
    Route::get('/test', [StudentApiController::class, 'testEndpoint']);
    
    // Test endpoint untuk debugging school data
    Route::get('/test-school-data', [StudentApiController::class, 'testSchoolData']);
    
    // Test endpoint untuk debugging registration
    Route::get('/test-registration', [StudentApiController::class, 'testRegistration']);
    
    // Get daftar mata pelajaran
    Route::get('/subjects', [StudentApiController::class, 'getAvailableSubjects']);
    
    // Get daftar sekolah
    Route::get('/schools', [StudentApiController::class, 'getAvailableSchools']);
    
    // Get status siswa berdasarkan NISN
    Route::get('/student-status/{nisn}', [StudentApiController::class, 'getStudentStatus']);
    
    // Registrasi siswa
    Route::post('/register', [StudentApiController::class, 'registerStudent']);
    
    // Ambil soal tes
    Route::post('/questions', [StudentApiController::class, 'getQuestions']);
    
    // Submit jawaban
    Route::post('/submit-answers', [StudentApiController::class, 'submitAnswers']);
    
    // Auto-save jawaban
    Route::post('/auto-save', [StudentApiController::class, 'autoSaveAnswer']);
    
    // Lihat hasil tes
    Route::get('/results/{testId}', [StudentApiController::class, 'getResults']);
    
    // Export PDF hasil
    Route::get('/export-pdf/{testId}', [StudentApiController::class, 'exportPdf']);
});
