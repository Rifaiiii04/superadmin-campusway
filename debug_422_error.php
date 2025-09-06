<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Debugging 422 error with exact data from frontend...\n\n";

// Data yang sama seperti di frontend (dari screenshot)
$testData = [
    'nisn' => '2233445566',
    'name' => 'Testing web',
    'kelas' => 'XII TKJ 2',
    'email' => 'web@gmail.com',
    'phone' => '0812311231',
    'parent_phone' => '0831231233',
    'password' => 'password123',
    'school_id' => 4
];

echo "📊 Test data (from frontend):\n";
foreach ($testData as $key => $value) {
    echo "  - {$key}: {$value}\n";
}
echo "\n";

// Check if school exists
$school = DB::table('schools')->where('id', 4)->first();
echo "🏫 School check:\n";
if ($school) {
    echo "  - School exists: YES\n";
    echo "  - School name: {$school->name}\n";
} else {
    echo "  - School exists: NO\n";
}
echo "\n";

// Check if NISN already exists
$existingNisn = DB::table('students')->where('nisn', '2233445566')->first();
echo "🔍 NISN check:\n";
if ($existingNisn) {
    echo "  - NISN already exists: YES\n";
    echo "  - Student name: {$existingNisn->name}\n";
} else {
    echo "  - NISN already exists: NO\n";
}
echo "\n";

// Test validation manually
$validator = \Illuminate\Support\Facades\Validator::make($testData, [
    'nisn' => 'required|string|size:10|unique:students,nisn',
    'name' => 'required|string|max:255',
    'kelas' => 'required|string|max:255',
    'email' => 'nullable|email|max:255',
    'phone' => 'nullable|string|max:20',
    'parent_phone' => 'nullable|string|max:20',
    'password' => 'required|string|min:6',
    'school_id' => 'required|exists:schools,id'
]);

echo "✅ Validation result:\n";
if ($validator->fails()) {
    echo "  - Valid: NO\n";
    echo "  - Errors:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "    - {$error}\n";
    }
} else {
    echo "  - Valid: YES\n";
}

echo "\n" . str_repeat("=", 50) . "\n";

// Test with controller
echo "🧪 Testing with controller...\n";

try {
    $controller = new \App\Http\Controllers\SchoolDashboardController();
    $request = new \Illuminate\Http\Request();
    $request->merge($testData);
    
    $response = $controller->addStudent($request);
    $responseData = $response->getData(true);
    
    echo "📡 Controller response:\n";
    echo "  - Status: {$response->getStatusCode()}\n";
    echo "  - Success: " . ($responseData['success'] ? 'true' : 'false') . "\n";
    echo "  - Message: {$responseData['message']}\n";
    
    if (isset($responseData['errors'])) {
        echo "  - Errors:\n";
        foreach ($responseData['errors'] as $field => $errors) {
            echo "    - {$field}: " . implode(', ', $errors) . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Controller error: {$e->getMessage()}\n";
    echo "  - Type: " . get_class($e) . "\n";
    echo "  - File: " . $e->getFile() . "\n";
    echo "  - Line: " . $e->getLine() . "\n";
}
