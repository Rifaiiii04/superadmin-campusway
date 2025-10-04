<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class SuperAdminController extends Controller
{
    /**
     * Show the super admin login form
     *
     * @return \Inertia\Response
     */
    public function showLogin()
    {
        // Render SuperAdmin/Login component - pastikan component ini ada di resources/js/Pages/SuperAdmin/Login.vue
        return Inertia::render('SuperAdmin/Login', [
            'title' => 'SuperAdmin Login',
            'version' => app()->version(),
        ]);
    }

    /**
     * Handle super admin login request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('username', 'password');

        // Attempt login dengan guard 'admin'
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect ke dashboard super admin
            return redirect()->intended('/super-admin/dashboard');
        }

        // Jika login gagal
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('username', 'remember'));
    }

    /**
     * Handle super admin logout
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/super-admin/login');
    }

    /**
     * Show super admin dashboard
     *
     * @return \Inertia\Response
     */
    public function dashboard()
    {
        return Inertia::render('SuperAdmin/Dashboard', [
            'title' => 'SuperAdmin Dashboard',
            'user' => Auth::guard('admin')->user(),
        ]);
    }

    /**
     * Test method untuk debugging
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function test()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'SuperAdminController is working!',
            'methods' => [
                'showLogin' => method_exists($this, 'showLogin'),
                'login' => method_exists($this, 'login'),
                'logout' => method_exists($this, 'logout'),
                'dashboard' => method_exists($this, 'dashboard'),
            ],
            'auth_check' => Auth::guard('admin')->check(),
        ]);
    }
}
