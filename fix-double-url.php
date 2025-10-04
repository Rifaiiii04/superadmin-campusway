<?php
/**
 * Fix Double URL Issue
 * This script will help identify and fix the double URL problem
 */

echo "🔧 Fixing Double URL Issue\n";
echo "=========================\n\n";

// Check if we're in Laravel context
if (!defined('LARAVEL_START')) {
    echo "❌ Not running in Laravel context\n";
    echo "Run this from Laravel root directory: php fix-double-url.php\n";
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

echo "2. Checking Routes:\n";
$routes = app('router')->getRoutes();
$problematicRoutes = [];

foreach ($routes as $route) {
    $uri = $route->uri();
    $methods = $route->methods();
    
    // Check for routes that might cause double URL
    if (strpos($uri, 'super-admin') !== false) {
        $problematicRoutes[] = [
            'uri' => $uri,
            'methods' => implode('|', $methods),
            'name' => $route->getName()
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

echo "\n5. Recommendations:\n";
echo "   - Ensure all routes use relative paths\n";
echo "   - Check Apache virtual host configuration\n";
echo "   - Verify Next.js is not intercepting /super-admin requests\n";
echo "   - Clear all caches after making changes\n";
echo "   - Rebuild Vite assets\n";

echo "\n6. Quick Fix Commands:\n";
echo "   php artisan config:clear\n";
echo "   php artisan route:clear\n";
echo "   php artisan view:clear\n";
echo "   php artisan cache:clear\n";
echo "   npm run build\n";
echo "   sudo systemctl restart apache2\n";

echo "\n✅ Analysis completed!\n";
?>
