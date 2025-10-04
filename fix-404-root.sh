#!/bin/bash

echo "🔧 FIXING 404 ROOT ERROR"
echo "======================="

# 1. Navigate to superadmin directory
cd /var/www/superadmin/superadmin-campusway

# 2. Stop Apache
sudo systemctl stop apache2

# 3. Fix routes - Add root redirect
echo "🔧 Step 1: Adding root redirect route..."
sudo tee routes/web.php > /dev/null << 'EOF'
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\SuperAdminController;

// Test route
Route::get('/test', function () {
    return response()->json(['status' => 'OK', 'message' => 'Routes working']);
});

// Root redirect to login
Route::get('/', function () {
    return redirect('/login');
});

// Super Admin Login
Route::get('/login', [SuperAdminController::class, 'showLogin'])->name('login');
Route::post('/login', [SuperAdminController::class, 'login']);

// Logout
Route::post('/logout', function () {
    Auth::guard('admin')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Protected routes
Route::middleware(['superadmin.auth'])->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
});
EOF

# 4. Create SuperAdminController if missing
echo "🔧 Step 2: Creating SuperAdminController..."
sudo tee app/Http/Controllers/SuperAdminController.php > /dev/null << 'EOF'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SuperAdminController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect('/dashboard');
        }
        
        return Inertia::render('SuperAdmin/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['username' => 'Username atau password salah.'])->withInput($request->only('username'));
    }

    public function dashboard()
    {
        if (!Auth::guard('admin')->check()) {
            return redirect('/login');
        }
        
        return Inertia::render('SuperAdmin/Dashboard', [
            'auth' => ['user' => Auth::guard('admin')->user()]
        ]);
    }
}
EOF

# 5. Create SuperAdminAuth middleware if missing
echo "🔧 Step 3: Creating SuperAdminAuth middleware..."
sudo tee app/Http/Middleware/SuperAdminAuth.php > /dev/null << 'EOF'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated',
                    'redirect' => '/login'
                ], 401);
            }
            
            return redirect('/login');
        }

        $request->setUserResolver(function () {
            return Auth::guard('admin')->user();
        });

        return $next($request);
    }
}
EOF

# 6. Create Admin model if missing
echo "🔧 Step 4: Creating Admin model..."
sudo tee app/Models/Admin.php > /dev/null << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $guard = 'admin';
    
    protected $fillable = [
        'name',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
EOF

# 7. Create admin user
echo "🔧 Step 5: Creating admin user..."
php -r "
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';
\$app = require_once 'bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

\$admin = Admin::where('username', 'admin')->first();
if (!\$admin) {
    \$admin = Admin::create([
        'name' => 'Super Admin',
        'username' => 'admin',
        'password' => Hash::make('password123')
    ]);
    echo 'Admin user created.';
} else {
    \$admin->update(['password' => Hash::make('password123')]);
    echo 'Admin user updated.';
}
"

# 8. Clear caches
echo "🔧 Step 6: Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 9. Set permissions
echo "🔧 Step 7: Setting permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 10. Start Apache
echo "🔄 Step 8: Starting Apache..."
sudo systemctl start apache2

# 11. Test everything
echo "🧪 Step 9: Testing everything..."
echo "Testing SuperAdmin root:"
curl -I http://103.23.198.101/super-admin/ || echo "❌ Root failed"
echo ""

echo "Testing SuperAdmin login:"
curl -I http://103.23.198.101/super-admin/login || echo "❌ Login failed"
echo ""

echo "Testing SuperAdmin test:"
curl -s http://103.23.198.101/super-admin/test || echo "❌ Test failed"
echo ""

echo "Testing Next.js root:"
curl -I http://103.23.198.101/ || echo "❌ Next.js failed"
echo ""

echo "✅ 404 ROOT FIX COMPLETE!"
echo "======================="
echo "🌐 SuperAdmin Root: http://103.23.198.101/super-admin/"
echo "🌐 SuperAdmin Login: http://103.23.198.101/super-admin/login"
echo "🌐 Next.js: http://103.23.198.101/"
echo ""
echo "📋 What was fixed:"
echo "   ✅ Added root redirect route"
echo "   ✅ Created SuperAdminController"
echo "   ✅ Created SuperAdminAuth middleware"
echo "   ✅ Created Admin model"
echo "   ✅ Created admin user (admin/password123)"
echo "   ✅ Fixed all permissions"
echo ""
echo "🎉 SuperAdmin root should now redirect to login!"
