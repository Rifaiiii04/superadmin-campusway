<?php
// =====================================================
// Check Password Algorithm
// =====================================================

echo "==========================================\n";
echo "Check Password Algorithm\n";
echo "==========================================\n\n";

// Bootstrap Laravel
require_once '/var/www/superadmin/superadmin-campusway/vendor/autoload.php';
$app = require_once '/var/www/superadmin/superadmin-campusway/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "✅ Laravel bootstrap: OK\n\n";

// Get school data
$school = App\Models\School::where('npsn', '11223345')->first();

if ($school) {
    echo "📋 School: {$school->name}\n";
    echo "📋 Password hash: {$school->password}\n";
    echo "📋 Hash length: " . strlen($school->password) . "\n";
    
    // Check if it's bcrypt
    if (strpos($school->password, '$2y$') === 0) {
        echo "✅ Password uses Bcrypt algorithm\n";
    } else {
        echo "❌ Password does NOT use Bcrypt algorithm\n";
    }
    
    // Test different password verification methods
    $test_password = 'password123';
    
    echo "\n🔍 Testing password verification methods:\n";
    
    // Method 1: Laravel Hash::check
    try {
        if (Hash::check($test_password, $school->password)) {
            echo "✅ Laravel Hash::check: OK\n";
        } else {
            echo "❌ Laravel Hash::check: FAILED\n";
        }
    } catch (Exception $e) {
        echo "❌ Laravel Hash::check: ERROR - " . $e->getMessage() . "\n";
    }
    
    // Method 2: PHP password_verify
    try {
        if (password_verify($test_password, $school->password)) {
            echo "✅ PHP password_verify: OK\n";
        } else {
            echo "❌ PHP password_verify: FAILED\n";
        }
    } catch (Exception $e) {
        echo "❌ PHP password_verify: ERROR - " . $e->getMessage() . "\n";
    }
    
    // Method 3: Check if it's a simple hash
    $common_hashes = [
        'md5' => md5($test_password),
        'sha1' => sha1($test_password),
        'sha256' => hash('sha256', $test_password),
        'sha512' => hash('sha512', $test_password)
    ];
    
    echo "\n🔍 Testing common hash algorithms:\n";
    foreach ($common_hashes as $algo => $hash) {
        if ($hash === $school->password) {
            echo "✅ Found matching algorithm: $algo\n";
            break;
        }
    }
    
    // Method 4: Try to rehash with bcrypt
    echo "\n🔍 Testing bcrypt rehash:\n";
    try {
        if (password_needs_rehash($school->password, PASSWORD_BCRYPT)) {
            echo "✅ Password needs rehash with bcrypt\n";
            $new_hash = password_hash($test_password, PASSWORD_BCRYPT);
            echo "📋 New bcrypt hash: " . substr($new_hash, 0, 20) . "...\n";
        } else {
            echo "❌ Password does not need rehash\n";
        }
    } catch (Exception $e) {
        echo "❌ Bcrypt rehash test: ERROR - " . $e->getMessage() . "\n";
    }
    
} else {
    echo "❌ School not found\n";
}

echo "\n==========================================\n";
echo "Password Check Complete!\n";
echo "==========================================\n";
?>
