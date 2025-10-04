<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminController;

// Test route sederhana
Route::get('/test', function () {
    return 'Hello World! Test berhasil!';
});

// Super Admin routes
Route::get('/login', [SuperAdminController::class, 'showLogin'])->name('login');
Route::post('/login', [SuperAdminController::class, 'login']);
Route::post('/logout', [SuperAdminController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['superadmin.auth'])->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
});
