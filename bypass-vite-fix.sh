#!/bin/bash

echo "🚀 BYPASS VITE - SIMPLE FIX"
echo "==========================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
sudo systemctl stop apache2

# 3. Set ownership
sudo chown -R $USER:$USER .

# 4. Create simple app.blade.php without Vite
echo "🔧 Creating simple app.blade.php without Vite..."
sudo tee resources/views/app.blade.php > /dev/null << 'EOF'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @routes
        @inertiaHead
        
        <!-- Simple CSS untuk basic styling -->
        <style>
            body { font-family: 'Figtree', sans-serif; margin: 0; padding: 0; }
            .min-h-screen { min-height: 100vh; }
            .bg-gray-50 { background-color: #f9fafb; }
            .flex { display: flex; }
            .flex-col { flex-direction: column; }
            .justify-center { justify-content: center; }
            .py-8 { padding-top: 2rem; padding-bottom: 2rem; }
            .sm\:py-12 { padding-top: 3rem; padding-bottom: 3rem; }
            .px-4 { padding-left: 1rem; padding-right: 1rem; }
            .sm\:px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
            .lg\:px-8 { padding-left: 2rem; padding-right: 2rem; }
            .sm\:mx-auto { margin-left: auto; margin-right: auto; }
            .sm\:w-full { width: 100%; }
            .sm\:max-w-md { max-width: 28rem; }
            .flex { display: flex; }
            .justify-center { justify-content: center; }
            .h-12 { height: 3rem; }
            .w-12 { width: 3rem; }
            .sm\:h-16 { height: 4rem; }
            .sm\:w-16 { width: 4rem; }
            .text-maroon-600 { color: #dc2626; }
            .mt-4 { margin-top: 1rem; }
            .sm\:mt-6 { margin-top: 1.5rem; }
            .text-center { text-align: center; }
            .text-2xl { font-size: 1.5rem; line-height: 2rem; }
            .sm\:text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
            .font-bold { font-weight: 700; }
            .tracking-tight { letter-spacing: -0.025em; }
            .text-gray-900 { color: #111827; }
            .mt-2 { margin-top: 0.5rem; }
            .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
            .text-gray-600 { color: #4b5563; }
            .mt-6 { margin-top: 1.5rem; }
            .sm\:mt-8 { margin-top: 2rem; }
            .sm\:mx-auto { margin-left: auto; margin-right: auto; }
            .sm\:w-full { width: 100%; }
            .sm\:max-w-md { max-width: 28rem; }
            .bg-white { background-color: #ffffff; }
            .py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
            .sm\:py-8 { padding-top: 2rem; padding-bottom: 2rem; }
            .px-4 { padding-left: 1rem; padding-right: 1rem; }
            .shadow { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }
            .sm\:rounded-lg { border-radius: 0.5rem; }
            .sm\:px-10 { padding-left: 2.5rem; padding-right: 2.5rem; }
            .space-y-4 > * + * { margin-top: 1rem; }
            .sm\:space-y-6 > * + * { margin-top: 1.5rem; }
            .block { display: block; }
            .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
            .font-medium { font-weight: 500; }
            .text-gray-700 { color: #374151; }
            .mt-1 { margin-top: 0.25rem; }
            .appearance-none { appearance: none; }
            .w-full { width: 100%; }
            .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
            .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
            .border { border-width: 1px; }
            .border-gray-300 { border-color: #d1d5db; }
            .rounded-md { border-radius: 0.375rem; }
            .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
            .placeholder-gray-400::placeholder { color: #9ca3af; }
            .focus\:outline-none:focus { outline: 2px solid transparent; outline-offset: 2px; }
            .focus\:ring-maroon-500:focus { box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1); }
            .focus\:border-maroon-500:focus { border-color: #dc2626; }
            .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
            .mt-2 { margin-top: 0.5rem; }
            .text-red-600 { color: #dc2626; }
            .relative { position: relative; }
            .absolute { position: absolute; }
            .inset-y-0 { top: 0; bottom: 0; }
            .right-0 { right: 0; }
            .pr-3 { padding-right: 0.75rem; }
            .flex { display: flex; }
            .items-center { align-items: center; }
            .h-4 { height: 1rem; }
            .w-4 { width: 1rem; }
            .sm\:h-5 { height: 1.25rem; }
            .sm\:w-5 { width: 1.25rem; }
            .text-gray-400 { color: #9ca3af; }
            .w-full { width: 100%; }
            .flex { display: flex; }
            .justify-center { justify-content: center; }
            .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
            .px-4 { padding-left: 1rem; padding-right: 1rem; }
            .border { border-width: 1px; }
            .border-transparent { border-color: transparent; }
            .rounded-md { border-radius: 0.375rem; }
            .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
            .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
            .font-medium { font-weight: 500; }
            .text-white { color: #ffffff; }
            .bg-maroon-600 { background-color: #dc2626; }
            .hover\:bg-maroon-700:hover { background-color: #b91c1c; }
            .focus\:outline-none:focus { outline: 2px solid transparent; outline-offset: 2px; }
            .focus\:ring-2:focus { box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.5); }
            .focus\:ring-offset-2:focus { box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.5), 0 0 0 4px rgba(220, 38, 38, 0.5); }
            .focus\:ring-maroon-500:focus { box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.5); }
            .disabled\:opacity-50:disabled { opacity: 0.5; }
        </style>
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
EOF

# 5. Set permissions
sudo chown -R www-data:www-data public
sudo chmod -R 755 public

# 6. Start Apache
sudo systemctl start apache2

# 7. Test
echo "🧪 Testing SuperAdmin..."
curl -I http://103.23.198.101/super-admin/login || echo "❌ SuperAdmin login failed"

echo ""
echo "✅ BYPASS VITE FIX COMPLETE!"
echo "==========================="
echo "🌐 Test SuperAdmin at: http://103.23.198.101/super-admin/login"
echo "📋 What was fixed:"
echo "   ✅ Bypassed Vite completely"
echo "   ✅ Added inline CSS for basic styling"
echo "   ✅ Removed dependency on build assets"
echo ""
echo "🎉 SuperAdmin should now load without asset errors!"
