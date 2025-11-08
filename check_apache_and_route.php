<?php
/**
 * Script to check Apache configuration and route accessibility
 * Run: php check_apache_and_route.php
 */

echo "=== Apache & Route Configuration Check ===\n\n";

// 1. Check if mod_rewrite is enabled
echo "1. Checking mod_rewrite...\n";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "   ✓ mod_rewrite is enabled\n";
    } else {
        echo "   ✗ mod_rewrite is NOT enabled\n";
    }
} else {
    echo "   ? Cannot check (not running in Apache context)\n";
}

// 2. Check .htaccess file
echo "\n2. Checking .htaccess file...\n";
$htaccessPath = __DIR__ . '/public/.htaccess';
if (file_exists($htaccessPath)) {
    echo "   ✓ .htaccess exists at: $htaccessPath\n";
    $content = file_get_contents($htaccessPath);
    if (strpos($content, 'RewriteEngine On') !== false) {
        echo "   ✓ RewriteEngine is enabled\n";
    } else {
        echo "   ✗ RewriteEngine is NOT enabled\n";
    }
} else {
    echo "   ✗ .htaccess NOT found\n";
}

// 3. Check Laravel routes
echo "\n3. Checking Laravel routes...\n";
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::create('/api/school/test-student-detail/40', 'GET')
);

echo "   Test route status: " . $response->getStatusCode() . "\n";
echo "   Response: " . $response->getContent() . "\n";

// 4. Check log files
echo "\n4. Checking log files...\n";
$laravelLog = __DIR__ . '/storage/logs/laravel.log';
$debugLog = __DIR__ . '/storage/logs/student_detail_debug.log';

if (file_exists($laravelLog)) {
    echo "   ✓ Laravel log exists\n";
    echo "   Last 5 lines:\n";
    $lines = file($laravelLog);
    foreach (array_slice($lines, -5) as $line) {
        echo "     " . trim($line) . "\n";
    }
} else {
    echo "   ✗ Laravel log NOT found\n";
}

if (file_exists($debugLog)) {
    echo "\n   ✓ Debug log exists\n";
    echo "   Last 10 lines:\n";
    $lines = file($debugLog);
    foreach (array_slice($lines, -10) as $line) {
        echo "     " . trim($line) . "\n";
    }
} else {
    echo "   ✗ Debug log NOT found\n";
}

// 5. Check database connection
echo "\n5. Checking database connection...\n";
try {
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST', 'localhost') . ';dbname=' . env('DB_DATABASE', 'campusway_db'),
        env('DB_USERNAME', 'root'),
        env('DB_PASSWORD', '')
    );
    echo "   ✓ Database connection successful\n";
    
    // Check if student 40 exists
    $stmt = $pdo->prepare("SELECT id, name FROM students WHERE id = ?");
    $stmt->execute([40]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($student) {
        echo "   ✓ Student 40 exists: " . $student['name'] . "\n";
    } else {
        echo "   ✗ Student 40 NOT found\n";
    }
} catch (Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n=== Check Complete ===\n";

