<?php
/**
 * Debug Login Script
 * Run this to check authentication configuration
 */

echo "🔍 TKA Super Admin Login Debug\n";
echo "==============================\n\n";

// Check if we're in Laravel context
if (!defined('LARAVEL_START')) {
    echo "❌ Not running in Laravel context\n";
    echo "Run this from Laravel root directory: php debug-login.php\n";
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
echo "   Admin Guard: " . (Config::get('auth.guards.admin.driver') ?? 'not set') . "\n";
echo "   Admin Provider: " . (Config::get('auth.guards.admin.provider') ?? 'not set') . "\n";
echo "   Admin Model: " . (Config::get('auth.providers.admins.model') ?? 'not set') . "\n\n";

echo "2. Checking Admin Model:\n";
try {
    $adminCount = Admin::count();
    echo "   ✅ Admin model accessible\n";
    echo "   📊 Total admins in database: $adminCount\n";
    
    if ($adminCount > 0) {
        $firstAdmin = Admin::first();
        echo "   👤 First admin: " . ($firstAdmin->username ?? 'no username') . "\n";
        echo "   🔑 Has password: " . (!empty($firstAdmin->password) ? 'Yes' : 'No') . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error accessing Admin model: " . $e->getMessage() . "\n";
}
echo "\n";

echo "3. Testing Authentication:\n";
try {
    // Test with dummy credentials
    $testCredentials = [
        'username' => 'test',
        'password' => 'test'
    ];
    
    $attempt = Auth::guard('admin')->attempt($testCredentials);
    echo "   🔐 Test login attempt: " . ($attempt ? 'Success' : 'Failed (expected)') . "\n";
    
    // Check if admin guard is working
    $isAuthenticated = Auth::guard('admin')->check();
    echo "   🔍 Admin guard check: " . ($isAuthenticated ? 'Authenticated' : 'Not authenticated') . "\n";
    
} catch (Exception $e) {
    echo "   ❌ Error testing authentication: " . $e->getMessage() . "\n";
}
echo "\n";

echo "4. Checking Session Configuration:\n";
echo "   Session Driver: " . Config::get('session.driver') . "\n";
echo "   Session Lifetime: " . Config::get('session.lifetime') . " minutes\n";
echo "   Session Domain: " . Config::get('session.domain') . "\n";
echo "   Session Path: " . Config::get('session.path') . "\n";
echo "   Session Secure: " . (Config::get('session.secure') ? 'Yes' : 'No') . "\n";
echo "   Session HttpOnly: " . (Config::get('session.http_only') ? 'Yes' : 'No') . "\n";
echo "   Session SameSite: " . Config::get('session.same_site') . "\n\n";

echo "5. Checking Routes:\n";
try {
    $routes = app('router')->getRoutes();
    $adminRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'login') !== false || strpos($uri, 'dashboard') !== false) {
            $adminRoutes[] = $uri . ' (' . implode('|', $route->methods()) . ')';
        }
    }
    
    echo "   📍 Login/Dashboard routes found:\n";
    foreach ($adminRoutes as $route) {
        echo "      - $route\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking routes: " . $e->getMessage() . "\n";
}
echo "\n";

echo "6. Checking Environment:\n";
echo "   APP_URL: " . env('APP_URL') . "\n";
echo "   APP_ENV: " . env('APP_ENV') . "\n";
echo "   APP_DEBUG: " . (env('APP_DEBUG') ? 'Yes' : 'No') . "\n";
echo "   SESSION_DOMAIN: " . env('SESSION_DOMAIN') . "\n\n";

echo "✅ Debug completed!\n";
echo "\n💡 If you see any errors above, fix them before testing login.\n";
echo "💡 Make sure you have at least one admin user in the database.\n";
echo "💡 Check that the admin guard is properly configured.\n";
?>
