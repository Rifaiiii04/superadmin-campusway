<?php
/**
 * Fix Double URL Final
 * This script will help fix the double URL issue
 */

echo "🔧 Fixing Double URL Issue - FINAL\n";
echo "==================================\n\n";

// Check if we're in Laravel context
if (!defined('LARAVEL_START')) {
    echo "❌ Not running in Laravel context\n";
    echo "Run this from Laravel root directory: php fix-double-url-final.php\n";
    exit(1);
}

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

echo "1. Checking Current URL Configuration:\n";
echo "   APP_URL: " . env('APP_URL') . "\n";
echo "   ASSET_URL: " . env('ASSET_URL') . "\n";
echo "   Current URL: " . URL::current() . "\n\n";

echo "2. Checking Routes for Double URL Issues:\n";
$routes = app('router')->getRoutes();
$problematicRoutes = [];

foreach ($routes as $route) {
    $uri = $route->uri();
    $methods = $route->methods();
    $name = $route->getName();
    
    // Check for routes that might cause double URL
    if (strpos($uri, 'super-admin') !== false) {
        $problematicRoutes[] = [
            'uri' => $uri,
            'methods' => implode('|', $methods),
            'name' => $name
        ];
    }
}

if (!empty($problematicRoutes)) {
    echo "   ⚠️  Found routes with 'super-admin' in URI:\n";
    foreach ($problematicRoutes as $route) {
        echo "      - {$route['uri']} ({$route['methods']}) - {$route['name']}\n";
    }
} else {
    echo "   ✅ No routes with 'super-admin' in URI found\n";
}

echo "\n3. Checking for Hardcoded URLs in Views:\n";
$viewPath = resource_path('views');
$jsPath = resource_path('js');

// Check for hardcoded URLs in Blade templates
$bladeFiles = glob($viewPath . '/**/*.blade.php');
$hardcodedUrls = [];

foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    if (strpos($content, '/super-admin') !== false) {
        $hardcodedUrls[] = str_replace($viewPath, '', $file);
    }
}

if (!empty($hardcodedUrls)) {
    echo "   ⚠️  Found hardcoded URLs in Blade templates:\n";
    foreach ($hardcodedUrls as $file) {
        echo "      - $file\n";
    }
} else {
    echo "   ✅ No hardcoded URLs in Blade templates\n";
}

// Check for hardcoded URLs in JS files
$jsFiles = glob($jsPath . '/**/*.js');
$jsxFiles = glob($jsPath . '/**/*.jsx');

$allJsFiles = array_merge($jsFiles, $jsxFiles);
$hardcodedJsUrls = [];

foreach ($allJsFiles as $file) {
    $content = file_get_contents($file);
    if (strpos($content, '/super-admin') !== false) {
        $hardcodedJsUrls[] = str_replace($jsPath, '', $file);
    }
}

if (!empty($hardcodedJsUrls)) {
    echo "   ⚠️  Found hardcoded URLs in JS/JSX files:\n";
    foreach ($hardcodedJsUrls as $file) {
        echo "      - $file\n";
    }
} else {
    echo "   ✅ No hardcoded URLs in JS/JSX files\n";
}

echo "\n4. Checking .htaccess Files:\n";
$htaccessFiles = [
    public_path('.htaccess'),
    base_path('.htaccess')
];

foreach ($htaccessFiles as $htaccess) {
    if (file_exists($htaccess)) {
        echo "   📄 Found: " . str_replace(base_path(), '', $htaccess) . "\n";
        $content = file_get_contents($htaccess);
        if (strpos($content, 'super-admin') !== false) {
            echo "      ⚠️  Contains 'super-admin' references\n";
        } else {
            echo "      ✅ No 'super-admin' references\n";
        }
    }
}

echo "\n5. Checking for Redirect Issues:\n";
// Check if there are any redirects in the application
$redirectRoutes = [];
foreach ($routes as $route) {
    $uri = $route->uri();
    $action = $route->getAction();
    
    if (isset($action['controller'])) {
        $controller = $action['controller'];
        if (strpos($controller, 'redirect') !== false) {
            $redirectRoutes[] = [
                'uri' => $uri,
                'controller' => $controller
            ];
        }
    }
}

if (!empty($redirectRoutes)) {
    echo "   ⚠️  Found routes with redirects:\n";
    foreach ($redirectRoutes as $route) {
        echo "      - {$route['uri']} -> {$route['controller']}\n";
    }
} else {
    echo "   ✅ No redirect routes found\n";
}

echo "\n6. Recommendations for Double URL Fix:\n";
echo "   - Check Apache virtual host configuration\n";
echo "   - Ensure /super-admin is properly aliased\n";
echo "   - Check if Next.js is intercepting /super-admin requests\n";
echo "   - Check for any .htaccess redirects\n";
echo "   - Clear all caches\n";
echo "   - Rebuild Vite assets\n";

echo "\n7. Quick Fix Commands:\n";
echo "   php artisan config:clear\n";
echo "   php artisan route:clear\n";
echo "   php artisan view:clear\n";
echo "   php artisan cache:clear\n";
echo "   npm run build\n";
echo "   sudo systemctl restart apache2\n";

echo "\n8. Apache Configuration Check:\n";
echo "   - Ensure Alias /super-admin points to Laravel public directory\n";
echo "   - Check if there are any RewriteRules causing double URL\n";
echo "   - Verify that Next.js is not handling /super-admin requests\n";

echo "\n✅ Double URL analysis completed!\n";
echo "\n💡 The double URL issue is likely caused by:\n";
echo "   1. Apache configuration problem\n";
echo "   2. Next.js intercepting /super-admin requests\n";
echo "   3. Redirect loops in .htaccess or routes\n";
echo "   4. Cached redirects\n";
?>
