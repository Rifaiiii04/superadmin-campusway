<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing add student API...\n\n";

// Get a test school
$school = DB::table('schools')->first();

if (!$school) {
    echo "❌ No schools found in database\n";
    exit;
}

echo "🏫 Test school found:\n";
echo "  - ID: {$school->id}\n";
echo "  - NPSN: {$school->npsn}\n";
echo "  - Name: {$school->name}\n\n";

// Test data
$testData = [
    'nisn' => '1234567890',
    'name' => 'Test Student Add',
    'kelas' => 'X IPA 1',
    'email' => 'test@example.com',
    'phone' => '081234567890',
    'parent_phone' => '081234567891',
    'password' => 'password123',
    'school_id' => $school->id
];

echo "📊 Test data:\n";
foreach ($testData as $key => $value) {
    echo "  - {$key}: {$value}\n";
}
echo "\n";

// Test the add method
$controller = new \App\Http\Controllers\SchoolDashboardController();

// Create a mock request
$request = new \Illuminate\Http\Request();
$request->merge($testData);

echo "🔍 Testing add student method...\n";

try {
    $response = $controller->addStudent($request);
    $responseData = $response->getData(true);
    
    echo "✅ Response received:\n";
    echo "  - Success: " . ($responseData['success'] ? 'true' : 'false') . "\n";
    echo "  - Message: " . $responseData['message'] . "\n";
    echo "  - Status Code: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() === 201) {
        echo "🎉 Student successfully added!\n";
        
        // Check if student exists in database
        $studentExists = DB::table('students')->where('nisn', $testData['nisn'])->exists();
        echo "  - Student exists in DB: " . ($studentExists ? 'YES' : 'NO') . "\n";
    } else {
        echo "❌ Add student failed\n";
    }
    
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "❌ Validation error:\n";
    foreach ($e->errors() as $field => $errors) {
        echo "  - {$field}: " . implode(', ', $errors) . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Error testing add: " . $e->getMessage() . "\n";
    echo "  - File: " . $e->getFile() . "\n";
    echo "  - Line: " . $e->getLine() . "\n";
}

echo "\n📊 Current student count: " . DB::table('students')->count() . "\n";
