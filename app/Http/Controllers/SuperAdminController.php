<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SuperAdminController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('SuperAdmin/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/super-admin/dashboard');
        }

        return back()->withErrors(['username' => 'Username atau password salah.'])->withInput($request->only('username'));
    }

    public function dashboard()
    {
        if (!Auth::guard('admin')->check()) {
            return redirect('/super-admin/login');
        }

        return Inertia::render('SuperAdmin/Dashboard', [
            'auth' => ['user' => Auth::guard('admin')->user()]
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/super-admin/login');
    }
}
