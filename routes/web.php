<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

// ===========================================
// SUPER ADMIN ROUTES WITH UI RENDERING
// ===========================================

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'framework' => 'Laravel', 
        'version' => app()->version(),
        'timestamp' => now()->toISOString()
    ]);
});

// Test endpoint
Route::get('/test', function () {
    return response()->json([
        'status' => 'SUCCESS',
        'message' => 'Laravel is working!',
        'bootstrap' => 'successful',
        'timestamp' => now()->toISOString()
    ]);
});

// SuperAdmin Login Page (UI)
Route::get('/login', function () {
    try {
        return Inertia::render('SuperAdmin/Login', [
            'title' => 'SuperAdmin Login',
            'version' => app()->version(),
        ]);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// SuperAdmin Login Process
Route::post('/login', function (Request $request) {
    try {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('username', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid credentials.',
        ])->withInput();
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// SuperAdmin Dashboard (UI)
Route::get('/dashboard', function () {
    try {
        if (!Auth::guard('admin')->check()) {
            return redirect('/login');
        }
        
        return Inertia::render('SuperAdmin/Dashboard', [
            'title' => 'SuperAdmin Dashboard',
            'user' => Auth::guard('admin')->user(),
        ]);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// SuperAdmin Logout
Route::post('/logout', function (Request $request) {
    try {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Root route redirect to login
Route::get('/', function () {
    return redirect('/login');
});
