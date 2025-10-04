<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

// Public API routes (no authentication required)
Route::prefix('web')->group(function () {
    Route::get('/schools', [ApiController::class, 'getSchools']);
    Route::get('/questions', [ApiController::class, 'getQuestions']);
    Route::get('/majors', [ApiController::class, 'getMajors']);
    Route::get('/health', [ApiController::class, 'healthCheck']);
});

// School API routes (with authentication)
Route::prefix('school')->middleware('auth:sanctum')->group(function () {
    Route::get('/schools', [ApiController::class, 'getSchools']);
    Route::get('/questions', [ApiController::class, 'getQuestions']);
    Route::get('/majors', [ApiController::class, 'getMajors']);
});
