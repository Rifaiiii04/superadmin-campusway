<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SchoolAuthController;
use App\Http\Controllers\SchoolDashboardController;
use App\Http\Controllers\SuperAdmin\TkaScheduleController;

// ===========================================
// SUPER ADMIN ROUTES WITH PREFIX
// ===========================================

// Group all super admin routes under /super-admin prefix
Route::prefix('super-admin')->group(function () {

    // ===========================================
    // PUBLIC ROUTES - FIXED
    // ===========================================

    // Redirect root to login
    Route::get('/', function () {
        return redirect('/super-admin/login');
    });

    // Login routes - FIXED: Sesuai dengan controller
    Route::get('/login', [SuperAdminController::class, 'showLogin'])->name('superadmin.login');
    Route::post('/login', [SuperAdminController::class, 'login'])->name('superadmin.login.post');

    // ===========================================
    // TEST ROUTES - UNTUK DEBUGGING
    // ===========================================
    
    Route::get('/test-simple', function () {
        return response()->json([
            'status' => 'success - no middleware',
            'time' => now(),
            'routes_working' => true,
            'controller' => 'SuperAdminController::showLogin exists: ' . method_exists(App\Http\Controllers\SuperAdminController::class, 'showLogin'),
            'assets_exists' => file_exists(public_path('build/assets/app-CAtf8d53.js'))
        ]);
    });

    Route::get('/debug', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'Laravel is working!',
            'routes' => [
                'login' => route('superadmin.login'),
                'assets' => url('/super-admin/build/assets/app-CAtf8d53.js')
            ],
            'controller_method' => 'SuperAdminController::showLogin',
            'inertia_working' => class_exists('Inertia\\Inertia')
        ]);
    });

    // Test route dengan Inertia sederhana
    Route::get('/test-inertia', function () {
        return Inertia::render('SuperAdmin/Login', [
            'test' => 'Working from test route'
        ]);
    });

    // ===========================================
    // PROTECTED ROUTES - DENGAN MIDDLEWARE
    // ===========================================
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('SuperAdmin/Dashboard');
        })->name('superadmin.dashboard');
        
        // Logout route
        Route::post('/logout', [SuperAdminController::class, 'logout'])->name('superadmin.logout');
        
        // Tambahkan routes protected lainnya di sini
    });
});

// ===========================================
// FALLBACK ROUTES
// ===========================================

// Fallback untuk root domain
Route::get('/', function () {
    return redirect('/super-admin/login');
});

// Fallback untuk 404
Route::fallback(function () {
    return redirect('/super-admin/login');
});
