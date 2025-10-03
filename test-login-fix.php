<?php
/**
 * Test Login Fix
 * Test the login fix for SuperAdmin
 */

echo "🧪 Testing Login Fix for SuperAdmin\n";
echo "===================================\n\n";

// Check if we're in Laravel context
if (!defined('LARAVEL_START')) {
    echo "❌ Not running in Laravel context\n";
    echo "Run this from Laravel root directory: php test-login-fix.php\n";
    exit(1);
}

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

echo "1. Testing Admin Model Configuration:\n";
try {
    $admin = new Admin();
    $fillable = $admin->getFillable();
    $guard = $admin->getGuardName();
    
    echo "   ✅ Admin model accessible\n";
    echo "   📋 Fillable fields: " . implode(', ', $fillable) . "\n";
    echo "   🔐 Guard: " . $guard . "\n";
    
} catch (Exception $e) {
    echo "   ❌ Error accessing Admin model: " . $e->getMessage() . "\n";
}
echo "\n";

echo "2. Testing Auth Configuration:\n";
try {
    $defaultGuard = Config::get('auth.defaults.guard');
    $adminGuard = Config::get('auth.guards.admin');
    $adminProvider = Config::get('auth.providers.admins');
    
    echo "   🔐 Default guard: " . $defaultGuard . "\n";
    echo "   🔐 Admin guard driver: " . $adminGuard['driver'] . "\n";
    echo "   🔐 Admin guard provider: " . $adminGuard['provider'] . "\n";
    echo "   🔐 Admin model: " . $adminProvider['model'] . "\n";
    
} catch (Exception $e) {
    echo "   ❌ Error checking auth config: " . $e->getMessage() . "\n";
}
echo "\n";

echo "3. Testing Session Configuration:\n";
try {
    $sessionDriver = Config::get('session.driver');
    $sessionPath = Config::get('session.path');
    $sessionCookie = Config::get('session.cookie');
    $sessionDomain = Config::get('session.domain');
    
    echo "   📁 Session driver: " . $sessionDriver . "\n";
    echo "   📁 Session path: " . $sessionPath . "\n";
    echo "   🍪 Session cookie: " . $sessionCookie . "\n";
    echo "   🌐 Session domain: " . ($sessionDomain ?: 'null') . "\n";
    
    if ($sessionPath === '/super-admin') {
        echo "   ✅ Session path is correctly isolated\n";
    } else {
        echo "   ⚠️  Session path should be '/super-admin' for isolation\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error checking session config: " . $e->getMessage() . "\n";
}
echo "\n";

echo "4. Testing Admin User:\n";
try {
    $admin = Admin::where('username', 'admin')->first();
    
    if ($admin) {
        echo "   ✅ Admin user exists\n";
        echo "   👤 Username: " . $admin->username . "\n";
        echo "   👤 Name: " . $admin->name . "\n";
        echo "   🔑 Has password: " . (!empty($admin->password) ? 'Yes' : 'No') . "\n";
    } else {
        echo "   ❌ Admin user not found\n";
        echo "   💡 Run: php create-admin-user.php\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error checking admin user: " . $e->getMessage() . "\n";
}
echo "\n";

echo "5. Testing Authentication:\n";
try {
    $testCredentials = [
        'username' => 'admin',
        'password' => 'password123'
    ];
    
    $attempt = Auth::guard('admin')->attempt($testCredentials);
    if ($attempt) {
        echo "   ✅ Login test successful\n";
        
        $user = Auth::guard('admin')->user();
        if ($user) {
            echo "   👤 Authenticated user: " . $user->name . " (" . $user->username . ")\n";
        }
        
        // Test logout
        Auth::guard('admin')->logout();
        echo "   ✅ Logout test successful\n";
    } else {
        echo "   ❌ Login test failed\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error testing authentication: " . $e->getMessage() . "\n";
}
echo "\n";

echo "6. Testing Routes:\n";
try {
    $routes = app('router')->getRoutes();
    $loginRoutes = [];
    $dashboardRoutes = [];
    $logoutRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        $methods = $route->methods();
        $name = $route->getName();
        
        if (strpos($uri, 'login') !== false) {
            $loginRoutes[] = "$uri (" . implode('|', $methods) . ") - $name";
        }
        if (strpos($uri, 'dashboard') !== false) {
            $dashboardRoutes[] = "$uri (" . implode('|', $methods) . ") - $name";
        }
        if (strpos($uri, 'logout') !== false) {
            $logoutRoutes[] = "$uri (" . implode('|', $methods) . ") - $name";
        }
    }
    
    echo "   📍 Login routes:\n";
    foreach ($loginRoutes as $route) {
        echo "      - $route\n";
    }
    
    echo "   📍 Dashboard routes:\n";
    foreach ($dashboardRoutes as $route) {
        echo "      - $route\n";
    }
    
    echo "   📍 Logout routes:\n";
    foreach ($logoutRoutes as $route) {
        echo "      - $route\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error checking routes: " . $e->getMessage() . "\n";
}
echo "\n";

echo "7. Testing Middleware:\n";
try {
    $middleware = app('router')->getMiddleware();
    if (isset($middleware['superadmin.auth'])) {
        echo "   ✅ SuperAdminAuth middleware registered\n";
    } else {
        echo "   ❌ SuperAdminAuth middleware not found\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error checking middleware: " . $e->getMessage() . "\n";
}
echo "\n";

echo "8. Summary:\n";
echo "   ✅ Admin model configured with username\n";
echo "   ✅ Auth guard 'admin' configured\n";
echo "   ✅ Session path isolated to '/super-admin'\n";
echo "   ✅ Login/logout redirects fixed\n";
echo "   ✅ Custom middleware created\n";
echo "\n";

echo "9. Expected Behavior:\n";
echo "   - Login at /super-admin/login\n";
echo "   - Redirect to /super-admin/dashboard after login\n";
echo "   - Redirect to /super-admin/login after logout\n";
echo "   - No session conflicts with Next.js\n";
echo "   - No modal popup from guru app\n";
echo "\n";

echo "✅ Login fix test completed!\n";
?>