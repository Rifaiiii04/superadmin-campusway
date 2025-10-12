<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Creating test school...\n";

try {
    // Check if school already exists
    $existingSchool = App\Models\School::where('npsn', '12345678')->first();
    
    if ($existingSchool) {
        echo "School with NPSN 12345678 already exists (ID: {$existingSchool->id})\n";
    } else {
        // Create test school
        $school = App\Models\School::create([
            'npsn' => '12345678',
            'name' => 'SMA Test School',
            'school_level' => 'SMA',
            'password' => bcrypt('password123')
        ]);
        
        echo "Created test school with ID: {$school->id}\n";
    }
    
    // Test dashboard API with the school
    $school = App\Models\School::where('npsn', '12345678')->first();
    if ($school) {
        $token = base64_encode($school->id . '|' . time() . '|' . $school->npsn);
        
        echo "Testing dashboard API with valid school...\n";
        echo "School ID: {$school->id}\n";
        echo "School NPSN: {$school->npsn}\n";
        echo "Token: " . substr($token, 0, 50) . "...\n";
        
        $url = 'http://103.23.198.101/super-admin/api/school/dashboard';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "HTTP Code: $httpCode\n";
        echo "Response: " . substr($response, 0, 500) . "\n";
        
        if ($httpCode == 200) {
            echo "✅ SUCCESS! Dashboard API is working!\n";
        } else {
            echo "❌ Still getting error\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Done.\n";
