<?php
/**
 * Test Authentication Fix
 * Test the authentication configuration after fixes
 */

echo "🔐 Testing Authentication Fix\n";
echo "============================\n\n";

// Check if we're in Laravel context
if (!defined('LARAVEL_START')) {
    echo "❌ Not running in Laravel context\n";
    echo "Run this from Laravel root directory: php test-auth-fix.php\n";
    exit(1);
}

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Models\Admin;

echo "1. Checking Auth Configuration:\n";
echo "   Default Guard: " . Config::get('auth.defaults.guard') . "\n";
echo "   Admin Guard Driver: " . Config::get('auth.guards.admin.driver') . "\n";
echo "   Admin Guard Provider: " . Config::get('auth.guards.admin.provider') . "\n";
echo "   Admin Guard Timeout: " . Config::get('auth.guards.admin.password_timeout') . "\n";
echo "   Admin Model: " . Config::get('auth.providers.admins.model') . "\n\n";

echo "2. Checking Admin Model:\n";
try {
    $adminCount = Admin::count();
    echo "   ✅ Admin model accessible\n";
    echo "   📊 Total admins in database: $adminCount\n";
    
    if ($adminCount > 0) {
        $firstAdmin = Admin::first();
        echo "   👤 First admin:\n";
        echo "      - Username: " . ($firstAdmin->username ?? 'N/A') . "\n";
        echo "      - Email: " . ($firstAdmin->email ?? 'N/A') . "\n";
        echo "      - Has password: " . (!empty($firstAdmin->password) ? 'Yes' : 'No') . "\n";
        echo "      - Created: " . ($firstAdmin->created_at ?? 'N/A') . "\n";
    } else {
        echo "   ⚠️  No admin users found in database\n";
        echo "   💡 Create admin user with: php artisan tinker\n";
        echo "      >>> App\\Models\\Admin::create([\n";
        echo "      >>>     'username' => 'admin',\n";
        echo "      >>>     'email' => 'admin@example.com',\n";
        echo "      >>>     'password' => bcrypt('password')\n";
        echo "      >>> ]);\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error accessing Admin model: " . $e->getMessage() . "\n";
}
echo "\n";

echo "3. Testing Authentication Guards:\n";
try {
    // Test admin guard
    $adminGuard = Auth::guard('admin');
    echo "   🔐 Admin guard: " . (is_object($adminGuard) ? 'OK' : 'Failed') . "\n";
    
    // Test web guard
    $webGuard = Auth::guard('web');
    echo "   🔐 Web guard: " . (is_object($webGuard) ? 'OK' : 'Failed') . "\n";
    
    // Check if guards are different
    echo "   🔍 Guards are different: " . ($adminGuard !== $webGuard ? 'Yes' : 'No') . "\n";
    
} catch (Exception $e) {
    echo "   ❌ Error testing guards: " . $e->getMessage() . "\n";
}
echo "\n";

echo "4. Testing Login Process:\n";
try {
    // Test with dummy credentials
    $testCredentials = [
        'email' => 'test@example.com',
        'password' => 'test'
    ];
    
    $attempt = Auth::guard('admin')->attempt($testCredentials);
    echo "   🔐 Test login attempt: " . ($attempt ? 'Success' : 'Failed (expected)') . "\n";
    
    // Check if admin guard is working
    $isAuthenticated = Auth::guard('admin')->check();
    echo "   🔍 Admin guard check: " . ($isAuthenticated ? 'Authenticated' : 'Not authenticated') . "\n";
    
    // Test session isolation
    $webAuthenticated = Auth::guard('web')->check();
    echo "   🔍 Web guard check: " . ($webAuthenticated ? 'Authenticated' : 'Not authenticated') . "\n";
    echo "   🔍 Session isolation: " . ($isAuthenticated !== $webAuthenticated ? 'Working' : 'Same session') . "\n";
    
} catch (Exception $e) {
    echo "   ❌ Error testing authentication: " . $e->getMessage() . "\n";
}
echo "\n";

echo "5. Testing Routes:\n";
try {
    $routes = app('router')->getRoutes();
    $loginRoutes = [];
    $dashboardRoutes = [];
    
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
    }
    
    echo "   📍 Login routes:\n";
    foreach ($loginRoutes as $route) {
        echo "      - $route\n";
    }
    
    echo "   📍 Dashboard routes:\n";
    foreach ($dashboardRoutes as $route) {
        echo "      - $route\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error checking routes: " . $e->getMessage() . "\n";
}
echo "\n";

echo "6. Testing Frontend Form:\n";
$loginFormPath = 'resources/js/Pages/SuperAdmin/Login.jsx';
if (file_exists($loginFormPath)) {
    $content = file_get_contents($loginFormPath);
    
    if (strpos($content, 'email') !== false) {
        echo "   ✅ Login form uses email field\n";
    } else {
        echo "   ❌ Login form still uses username field\n";
    }
    
    if (strpos($content, 'type="email"') !== false) {
        echo "   ✅ Email input has correct type\n";
    } else {
        echo "   ❌ Email input missing type attribute\n";
    }
    
    if (strpos($content, 'errors.email') !== false) {
        echo "   ✅ Error handling for email field\n";
    } else {
        echo "   ❌ Missing error handling for email field\n";
    }
} else {
    echo "   ❌ Login form file not found\n";
}
echo "\n";

echo "7. Recommendations:\n";
echo "   - Ensure admin user exists with email field\n";
echo "   - Test login with email/password combination\n";
echo "   - Verify session isolation between guards\n";
echo "   - Check that dashboard requires admin authentication\n";
echo "   - Clear all caches after making changes\n";

echo "\n✅ Authentication fix test completed!\n";
?>
