<?php

echo "Testing Laravel Server...\n\n";

// Test 1: Check if server is responding
echo "1. Testing server response...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ Error: $error\n";
} else {
    echo "✅ Server responding with HTTP $httpCode\n";
    if ($httpCode == 200) {
        echo "✅ Laravel application is working!\n";
    }
}

echo "\n2. Testing database connection...\n";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    DB::connection()->getPdo();
    echo "✅ Database connection successful!\n";
    echo "✅ Using MSSQL database: " . config('database.connections.sqlsrv.database') . "\n";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n3. Testing migrations...\n";
try {
    $migrations = DB::table('migrations')->count();
    echo "✅ Migrations table exists with $migrations records\n";
} catch (Exception $e) {
    echo "❌ Migrations error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Test completed!\n";

?>
