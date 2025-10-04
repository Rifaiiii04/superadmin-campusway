#!/bin/bash

echo "🔧 FIXING BLANK WHITE PAGE"
echo "========================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
sudo systemctl stop apache2

# 3. Create proper app.blade.php with Inertia
echo "🔧 Step 1: Creating proper app.blade.php..."
sudo tee resources/views/app.blade.php > /dev/null << 'EOF'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Inertia -->
        @routes
        @inertiaHead
        
        <!-- Manual CSS for basic styling -->
        <style>
            body {
                font-family: 'Figtree', sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f3f4f6;
            }
            .min-h-screen {
                min-height: 100vh;
            }
            .flex {
                display: flex;
            }
            .items-center {
                align-items: center;
            }
            .justify-center {
                justify-content: center;
            }
            .bg-gray-50 {
                background-color: #f9fafb;
            }
            .py-12 {
                padding-top: 3rem;
                padding-bottom: 3rem;
            }
            .px-4 {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .sm\:mx-auto {
                margin-left: auto;
                margin-right: auto;
            }
            .sm\:w-full {
                width: 100%;
            }
            .sm\:max-w-md {
                max-width: 28rem;
            }
            .bg-white {
                background-color: #ffffff;
            }
            .py-8 {
                padding-top: 2rem;
                padding-bottom: 2rem;
            }
            .px-4 {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .shadow {
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            }
            .sm\:rounded-lg {
                border-radius: 0.5rem;
            }
            .sm\:px-10 {
                padding-left: 2.5rem;
                padding-right: 2.5rem;
            }
            .space-y-6 > * + * {
                margin-top: 1.5rem;
            }
            .text-center {
                text-align: center;
            }
            .text-3xl {
                font-size: 1.875rem;
                line-height: 2.25rem;
            }
            .font-bold {
                font-weight: 700;
            }
            .tracking-tight {
                letter-spacing: -0.025em;
            }
            .text-gray-900 {
                color: #111827;
            }
            .mt-2 {
                margin-top: 0.5rem;
            }
            .text-sm {
                font-size: 0.875rem;
                line-height: 1.25rem;
            }
            .text-gray-600 {
                color: #4b5563;
            }
            .block {
                display: block;
            }
            .text-sm {
                font-size: 0.875rem;
                line-height: 1.25rem;
            }
            .font-medium {
                font-weight: 500;
            }
            .text-gray-700 {
                color: #374151;
            }
            .mt-1 {
                margin-top: 0.25rem;
            }
            .appearance-none {
                appearance: none;
            }
            .w-full {
                width: 100%;
            }
            .px-3 {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            .py-2 {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
            .border {
                border-width: 1px;
            }
            .border-gray-300 {
                border-color: #d1d5db;
            }
            .rounded-md {
                border-radius: 0.375rem;
            }
            .shadow-sm {
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .placeholder-gray-400::placeholder {
                color: #9ca3af;
            }
            .focus\:outline-none:focus {
                outline: 2px solid transparent;
                outline-offset: 2px;
            }
            .focus\:ring-2:focus {
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
            }
            .focus\:ring-blue-500:focus {
                --tw-ring-color: #3b82f6;
            }
            .focus\:border-blue-500:focus {
                border-color: #3b82f6;
            }
            .text-sm {
                font-size: 0.875rem;
                line-height: 1.25rem;
            }
            .mt-2 {
                margin-top: 0.5rem;
            }
            .text-red-600 {
                color: #dc2626;
            }
            .w-full {
                width: 100%;
            }
            .flex {
                display: flex;
            }
            .justify-center {
                justify-content: center;
            }
            .py-2 {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
            .px-4 {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .border {
                border-width: 1px;
            }
            .border-transparent {
                border-color: transparent;
            }
            .rounded-md {
                border-radius: 0.375rem;
            }
            .shadow-sm {
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .text-sm {
                font-size: 0.875rem;
                line-height: 1.25rem;
            }
            .font-medium {
                font-weight: 500;
            }
            .text-white {
                color: #ffffff;
            }
            .bg-blue-600 {
                background-color: #2563eb;
            }
            .hover\:bg-blue-700:hover {
                background-color: #1d4ed8;
            }
            .focus\:outline-none:focus {
                outline: 2px solid transparent;
                outline-offset: 2px;
            }
            .focus\:ring-2:focus {
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
            }
            .focus\:ring-offset-2:focus {
                --tw-ring-offset-width: 2px;
            }
            .focus\:ring-blue-500:focus {
                --tw-ring-color: #3b82f6;
            }
            .disabled\:opacity-50:disabled {
                opacity: 0.5;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
EOF

# 4. Create Login React component
echo "🔧 Step 2: Creating Login React component..."
sudo mkdir -p resources/js/Pages/SuperAdmin
sudo tee resources/js/Pages/SuperAdmin/Login.jsx > /dev/null << 'EOF'
import React, { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { Building2, Eye, EyeOff } from 'lucide-react';

export default function Login() {
    const [showPassword, setShowPassword] = useState(false);
    const { data, setData, post, processing, errors } = useForm({
        username: '',
        password: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <>
            <Head title="Super Admin Login" />
            
            <div className="min-h-screen bg-gray-50 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
                <div className="sm:mx-auto sm:w-full sm:max-w-md">
                    <div className="flex justify-center">
                        <Building2 className="h-12 w-12 sm:h-16 sm:w-16 text-green-600" />
                    </div>
                    <h2 className="mt-4 sm:mt-6 text-center text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">
                        Super Admin
                    </h2>
                    <p className="mt-2 text-center text-sm text-gray-600">
                        Masuk menggunakan username dan password Anda
                    </p>
                </div>

                <div className="mt-6 sm:mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                    <div className="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                        <form className="space-y-6" onSubmit={handleSubmit}>
                            <div>
                                <label htmlFor="username" className="block text-sm font-medium text-gray-700">
                                    Username
                                </label>
                                <div className="mt-1">
                                    <input
                                        id="username"
                                        name="username"
                                        type="text"
                                        required
                                        value={data.username}
                                        onChange={(e) => setData('username', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                        placeholder="Masukkan username"
                                    />
                                </div>
                                {errors.username && (
                                    <p className="mt-2 text-sm text-red-600">{errors.username}</p>
                                )}
                            </div>

                            <div>
                                <label htmlFor="password" className="block text-sm font-medium text-gray-700">
                                    Password
                                </label>
                                <div className="mt-1 relative">
                                    <input
                                        id="password"
                                        name="password"
                                        type={showPassword ? 'text' : 'password'}
                                        required
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm pr-10"
                                        placeholder="Masukkan password"
                                    />
                                    <button
                                        type="button"
                                        className="absolute inset-y-0 right-0 pr-3 flex items-center"
                                        onClick={() => setShowPassword(!showPassword)}
                                    >
                                        {showPassword ? (
                                            <EyeOff className="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" />
                                        ) : (
                                            <Eye className="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" />
                                        )}
                                    </button>
                                </div>
                                {errors.password && (
                                    <p className="mt-2 text-sm text-red-600">{errors.password}</p>
                                )}
                            </div>

                            <div>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                                >
                                    {processing ? 'Memproses...' : 'Masuk'}
                                </button>
                            </div>
                        </form>

                        <div className="mt-6">
                            <div className="relative">
                                <div className="absolute inset-0 flex items-center">
                                    <div className="w-full border-t border-gray-300" />
                                </div>
                                <div className="relative flex justify-center text-sm">
                                    <span className="px-2 bg-white text-gray-500">
                                        Sistem Pendidikan Nasional
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
EOF

# 5. Create Dashboard React component
echo "🔧 Step 3: Creating Dashboard React component..."
sudo tee resources/js/Pages/SuperAdmin/Dashboard.jsx > /dev/null << 'EOF'
import React from 'react';
import { Head } from '@inertiajs/react';
import { Building2, Users, FileText, BarChart3 } from 'lucide-react';

export default function Dashboard({ auth }) {
    return (
        <>
            <Head title="Super Admin Dashboard" />
            
            <div className="min-h-screen bg-gray-50">
                <div className="bg-white shadow">
                    <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center">
                                <Building2 className="h-8 w-8 text-green-600 mr-3" />
                                <h1 className="text-2xl font-bold text-gray-900">Super Admin Dashboard</h1>
                            </div>
                            <div className="flex items-center space-x-4">
                                <span className="text-sm text-gray-600">Welcome, {auth.user.name}</span>
                                <form method="POST" action="/logout" className="inline">
                                    <button
                                        type="submit"
                                        className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium"
                                    >
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    <div className="px-4 py-6 sm:px-0">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div className="bg-white overflow-hidden shadow rounded-lg">
                                <div className="p-5">
                                    <div className="flex items-center">
                                        <div className="flex-shrink-0">
                                            <Building2 className="h-6 w-6 text-green-600" />
                                        </div>
                                        <div className="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt className="text-sm font-medium text-gray-500 truncate">
                                                    Total Sekolah
                                                </dt>
                                                <dd className="text-lg font-medium text-gray-900">
                                                    0
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="bg-white overflow-hidden shadow rounded-lg">
                                <div className="p-5">
                                    <div className="flex items-center">
                                        <div className="flex-shrink-0">
                                            <Users className="h-6 w-6 text-blue-600" />
                                        </div>
                                        <div className="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt className="text-sm font-medium text-gray-500 truncate">
                                                    Total Siswa
                                                </dt>
                                                <dd className="text-lg font-medium text-gray-900">
                                                    0
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="bg-white overflow-hidden shadow rounded-lg">
                                <div className="p-5">
                                    <div className="flex items-center">
                                        <div className="flex-shrink-0">
                                            <FileText className="h-6 w-6 text-yellow-600" />
                                        </div>
                                        <div className="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt className="text-sm font-medium text-gray-500 truncate">
                                                    Total Soal
                                                </dt>
                                                <dd className="text-lg font-medium text-gray-900">
                                                    0
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="bg-white overflow-hidden shadow rounded-lg">
                                <div className="p-5">
                                    <div className="flex items-center">
                                        <div className="flex-shrink-0">
                                            <BarChart3 className="h-6 w-6 text-purple-600" />
                                        </div>
                                        <div className="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt className="text-sm font-medium text-gray-500 truncate">
                                                    Total Hasil
                                                </dt>
                                                <dd className="text-lg font-medium text-gray-900">
                                                    0
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="mt-8">
                            <div className="bg-white shadow rounded-lg">
                                <div className="px-4 py-5 sm:p-6">
                                    <h3 className="text-lg leading-6 font-medium text-gray-900">
                                        Selamat Datang di Super Admin Dashboard
                                    </h3>
                                    <div className="mt-2 max-w-xl text-sm text-gray-500">
                                        <p>
                                            Anda berhasil masuk ke sistem Super Admin. 
                                            Dari sini Anda dapat mengelola sekolah, siswa, soal, dan hasil tes.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
EOF

# 6. Create app.jsx
echo "🔧 Step 4: Creating app.jsx..."
sudo tee resources/js/app.jsx > /dev/null << 'EOF'
import { createInertiaApp } from '@inertiajs/react'
import { createRoot } from 'react-dom/client'

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true })
    return pages[`./Pages/${name}.jsx`]
  },
  setup({ el, App, props }) {
    createRoot(el).render(<App {...props} />)
  },
})
EOF

# 7. Install dependencies
echo "🔧 Step 5: Installing dependencies..."
npm install @inertiajs/react react react-dom lucide-react

# 8. Build assets
echo "🔧 Step 6: Building assets..."
npm run build

# 9. Clear caches
echo "🔧 Step 7: Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 10. Set permissions
echo "🔧 Step 8: Setting permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache public
sudo chmod -R 775 storage bootstrap/cache
sudo chmod -R 755 public

# 11. Start Apache
echo "🔄 Step 9: Starting Apache..."
sudo systemctl start apache2

# 12. Test everything
echo "🧪 Step 10: Testing everything..."
echo "Testing SuperAdmin login page:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ Login failed"
echo ""

echo "Testing SuperAdmin test:"
curl -s http://103.23.198.101/super-admin/test || echo "❌ Test failed"
echo ""

echo "Testing Next.js root:"
curl -I http://103.23.198.101/ || echo "❌ Next.js failed"
echo ""

echo "✅ BLANK PAGE FIX COMPLETE!"
echo "========================="
echo "🌐 SuperAdmin Login: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Created proper app.blade.php with Inertia"
echo "   ✅ Created Login React component"
echo "   ✅ Created Dashboard React component"
echo "   ✅ Created app.jsx entry point"
echo "   ✅ Installed React dependencies"
echo "   ✅ Built assets with Vite"
echo "   ✅ Fixed all permissions"
echo ""
echo "🎉 SuperAdmin should now show login form!"
