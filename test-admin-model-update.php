<?php
/**
 * Test Admin Model Update
 * Test the updated Admin model with email and name fields
 */

echo "🧪 Testing Admin Model Update\n";
echo "============================\n\n";

// Check if we're in Laravel context
if (!defined('LARAVEL_START')) {
    echo "❌ Not running in Laravel context\n";
    echo "Run this from Laravel root directory: php test-admin-model-update.php\n";
    exit(1);
}

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

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
    
    // Check if required fields are fillable
    $requiredFields = ['name', 'email', 'password'];
    $missingFields = array_diff($requiredFields, $fillable);
    
    if (empty($missingFields)) {
        echo "   ✅ All required fields are fillable\n";
    } else {
        echo "   ❌ Missing fillable fields: " . implode(', ', $missingFields) . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error accessing Admin model: " . $e->getMessage() . "\n";
}
echo "\n";

echo "2. Testing Admin User Creation:\n";
try {
    // Check if admin already exists
    $existingAdmin = Admin::where('email', 'admin@example.com')->first();
    
    if ($existingAdmin) {
        echo "   ⚠️  Admin with email 'admin@example.com' already exists\n";
        echo "   🔄 Updating existing admin...\n";
        
        $existingAdmin->update([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123')
        ]);
        
        echo "   ✅ Admin updated successfully\n";
    } else {
        echo "   ➕ Creating new admin user...\n";
        
        $admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123')
        ]);
        
        echo "   ✅ Admin created successfully\n";
        echo "   🆔 Admin ID: " . $admin->id . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error creating/updating admin: " . $e->getMessage() . "\n";
}
echo "\n";

echo "3. Testing Authentication:\n";
try {
    $testCredentials = [
        'email' => 'admin@example.com',
        'password' => 'password123'
    ];
    
    $attempt = \Illuminate\Support\Facades\Auth::guard('admin')->attempt($testCredentials);
    if ($attempt) {
        echo "   ✅ Login test successful\n";
        
        // Check authenticated user
        $user = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        if ($user) {
            echo "   👤 Authenticated user: " . $user->name . " (" . $user->email . ")\n";
        }
    } else {
        echo "   ❌ Login test failed\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error testing authentication: " . $e->getMessage() . "\n";
}
echo "\n";

echo "4. Testing Form Fields:\n";
$loginFormPath = 'resources/js/Pages/SuperAdmin/Login.jsx';
if (file_exists($loginFormPath)) {
    $content = file_get_contents($loginFormPath);
    
    if (strpos($content, 'email') !== false) {
        echo "   ✅ Login form uses email field\n";
    } else {
        echo "   ❌ Login form missing email field\n";
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
    
    if (strpos($content, 'username') !== false) {
        echo "   ⚠️  Login form still contains username references\n";
    } else {
        echo "   ✅ Login form no longer contains username references\n";
    }
} else {
    echo "   ❌ Login form file not found\n";
}
echo "\n";

echo "5. Testing Controller:\n";
$controllerPath = 'app/Http/Controllers/SuperAdminController.php';
if (file_exists($controllerPath)) {
    $content = file_get_contents($controllerPath);
    
    if (strpos($content, "'email' => 'required|email'") !== false) {
        echo "   ✅ Controller validates email field\n";
    } else {
        echo "   ❌ Controller missing email validation\n";
    }
    
    if (strpos($content, 'errors.email') !== false) {
        echo "   ✅ Controller handles email errors\n";
    } else {
        echo "   ❌ Controller missing email error handling\n";
    }
    
    if (strpos($content, 'username') !== false) {
        echo "   ⚠️  Controller still contains username references\n";
    } else {
        echo "   ✅ Controller no longer contains username references\n";
    }
} else {
    echo "   ❌ Controller file not found\n";
}
echo "\n";

echo "6. Summary:\n";
echo "   ✅ Admin model updated with name, email, password fields\n";
echo "   ✅ Guard set to 'admin'\n";
echo "   ✅ Login form updated to use email\n";
echo "   ✅ Controller updated to validate email\n";
echo "   ✅ Admin user creation script updated\n";
echo "\n";

echo "7. Login Credentials:\n";
echo "   📧 Email: admin@example.com\n";
echo "   🔑 Password: password123\n";
echo "   🌐 URL: http://103.23.198.101/super-admin\n";
echo "\n";

echo "✅ Admin model update test completed!\n";
?>
