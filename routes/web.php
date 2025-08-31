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
    
    // Protected routes (require auth)
    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/schools', [SuperAdminController::class, 'schools'])->name('schools');
        Route::post('/schools', [SuperAdminController::class, 'storeSchool'])->name('schools.store');
        Route::put('/schools/{school}', [SuperAdminController::class, 'updateSchool'])->name('schools.update');
        Route::delete('/schools/{school}', [SuperAdminController::class, 'deleteSchool'])->name('schools.delete');
        Route::post('/schools/import', [SuperAdminController::class, 'importSchools'])->name('schools.import');

        Route::get('/questions', [SuperAdminController::class, 'questions'])->name('questions');
        Route::post('/questions', [SuperAdminController::class, 'storeQuestion'])->name('questions.store');
        Route::put('/questions/{question}', [SuperAdminController::class, 'updateQuestion'])->name('questions.update');
        Route::delete('/questions/{question}', [SuperAdminController::class, 'deleteQuestion'])->name('questions.delete');

        Route::get('/monitoring', [SuperAdminController::class, 'monitoring'])->name('monitoring');
                        Route::get('/reports', [SuperAdminController::class, 'reports'])->name('reports');
                Route::post('/reports/download', [SuperAdminController::class, 'downloadReport'])->name('reports.download');
                                Route::post('/logout', [SuperAdminController::class, 'logout'])->name('logout');
                
                // Media upload route
                Route::post('/upload-media', [MediaController::class, 'upload'])->name('media.upload');
            });
        });

require __DIR__.'/auth.php';
