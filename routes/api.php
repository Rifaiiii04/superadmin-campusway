<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentWebController;

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
