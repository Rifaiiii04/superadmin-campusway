<?php
/**
 * Create Admin User
 * Create admin user with email for testing
 */

echo "👤 Creating Admin User\n";
echo "=====================\n\n";

// Check if we're in Laravel context
if (!defined('LARAVEL_START')) {
    echo "❌ Not running in Laravel context\n";
    echo "Run this from Laravel root directory: php create-admin-user.php\n";
    exit(1);
}

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

echo "1. Checking existing admin users:\n";
$existingAdmins = Admin::all();
echo "   📊 Found " . $existingAdmins->count() . " existing admin users\n";

foreach ($existingAdmins as $admin) {
    echo "   👤 - " . ($admin->username ?? 'N/A') . " (" . ($admin->email ?? 'N/A') . ")\n";
}

echo "\n2. Creating new admin user:\n";

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
    
    echo "\n3. Admin credentials:\n";
    echo "   👤 Name: Super Admin\n";
    echo "   📧 Email: admin@example.com\n";
    echo "   🔑 Password: password123\n";
    
    echo "\n4. Testing login:\n";
    $testCredentials = [
        'email' => 'admin@example.com',
        'password' => 'password123'
    ];
    
    $attempt = \Illuminate\Support\Facades\Auth::guard('admin')->attempt($testCredentials);
    if ($attempt) {
        echo "   ✅ Login test successful\n";
    } else {
        echo "   ❌ Login test failed\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error creating admin: " . $e->getMessage() . "\n";
    echo "   💡 Make sure database is properly configured\n";
}

echo "\n✅ Admin user creation completed!\n";
echo "\n💡 You can now login with:\n";
echo "   URL: http://103.23.198.101/super-admin\n";
echo "   Email: admin@example.com\n";
echo "   Password: password123\n";
?>
