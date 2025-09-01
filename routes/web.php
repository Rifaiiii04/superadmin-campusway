<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\MediaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Super Admin Routes
Route::prefix('super-admin')->name('super-admin.')->group(function () {
    // Public login route (no auth required)
    Route::get('/login', [SuperAdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [SuperAdminController::class, 'login'])->name('login.post');
    
    // Import routes (no CSRF required, but still protected by admin auth)
    Route::middleware(['admin.auth.nocsrf'])->group(function () {
        Route::post('/questions/import', [SuperAdminController::class, 'importQuestions'])->name('questions.import');
        Route::post('/schools/import', [SuperAdminController::class, 'importSchools'])->name('schools.import');
    });
    
    // Protected routes (require auth)
    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/schools', [SuperAdminController::class, 'schools'])->name('schools');
        Route::get('/schools/{id}', [SuperAdminController::class, 'schoolDetail'])->name('schools.detail');
        Route::post('/schools', [SuperAdminController::class, 'storeSchool'])->name('schools.store');
        Route::put('/schools/{school}', [SuperAdminController::class, 'updateSchool'])->name('schools.update');
        Route::delete('/schools/{school}', [SuperAdminController::class, 'deleteSchool'])->name('schools.delete');
        Route::get('/schools/import-test', [SuperAdminController::class, 'importSchoolsTest'])->name('schools.import.test');

        Route::get('/questions', [SuperAdminController::class, 'questions'])->name('questions');
        Route::post('/questions', [SuperAdminController::class, 'storeQuestion'])->name('questions.store');
        Route::get('/questions/import-test', [SuperAdminController::class, 'importTest'])->name('questions.import.test');

        Route::put('/questions/{question}', [SuperAdminController::class, 'updateQuestion'])->name('questions.update');
        Route::delete('/questions/{question}', [SuperAdminController::class, 'deleteQuestion'])->name('questions.delete');

        Route::get('/monitoring', [SuperAdminController::class, 'monitoring'])->name('monitoring');
        Route::get('/monitoring/data', [SuperAdminController::class, 'getMonitoringData'])->name('monitoring.data');
        Route::get('/reports', [SuperAdminController::class, 'reports'])->name('reports');
        Route::post('/reports/download', [SuperAdminController::class, 'downloadReport'])->name('reports.download');
        Route::get('/reports/test', [SuperAdminController::class, 'testDownload'])->name('reports.test');
        
        // Major Recommendations
        Route::get('/major-recommendations', [SuperAdminController::class, 'majorRecommendations'])->name('major-recommendations');
        Route::post('/major-recommendations', [SuperAdminController::class, 'storeMajorRecommendation'])->name('major-recommendations.store');
        
        // Coming Soon Pages
        Route::get('/questions', [SuperAdminController::class, 'questions'])->name('questions');
        Route::get('/results', [SuperAdminController::class, 'results'])->name('results');
        Route::put('/major-recommendations/{id}', [SuperAdminController::class, 'updateMajorRecommendation'])->name('major-recommendations.update');
        Route::delete('/major-recommendations/{id}', [SuperAdminController::class, 'deleteMajorRecommendation'])->name('major-recommendations.delete');
        Route::patch('/major-recommendations/{id}/toggle', [SuperAdminController::class, 'toggleMajorRecommendation'])->name('major-recommendations.toggle');
        Route::get('/major-recommendations/export', [SuperAdminController::class, 'exportMajorRecommendations'])->name('major-recommendations.export');
        
        Route::post('/logout', [SuperAdminController::class, 'logout'])->name('logout');
        
        // Media upload route
        Route::post('/upload-media', [MediaController::class, 'upload'])->name('media.upload');
    });
});

require __DIR__.'/auth.php';
