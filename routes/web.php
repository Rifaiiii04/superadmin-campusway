<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ===========================================
// CORRECT ROUTES - WITHOUT /super-admin PREFIX
// ===========================================

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'framework' => 'Laravel', 
        'version' => app()->version(),
        'timestamp' => now()->toISOString()
    ]);
});

Route::get('/test', function () {
    return response()->json([
        'status' => 'SUCCESS',
        'message' => 'Laravel is working!',
        'bootstrap' => 'successful',
        'timestamp' => now()->toISOString()
    ]);
});

Route::get('/login', function () {
    try {
        return response()->json([
            'status' => 'login_page',
            'message' => 'Login endpoint reached',
            'timestamp' => now()->toISOString()
        ]);
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});

// Root route for /super-admin
Route::get('/', function () {
    return response()->json([
        'status' => 'super_admin_home', 
        'message' => 'Super Admin application is running',
        'timestamp' => now()->toISOString()
    ]);
});
