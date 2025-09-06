<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing delete student API...\n\n";

// Get a test student
$student = DB::table('students')->first();

if (!$student) {
    echo "❌ No students found in database\n";
    exit;
}

echo "📊 Test student found:\n";
echo "  - ID: {$student->id}\n";
echo "  - Name: {$student->name}\n";
echo "  - NISN: {$student->nisn}\n";
echo "  - School ID: {$student->school_id}\n\n";

// Test the delete method
$controller = new \App\Http\Controllers\SchoolDashboardController();

// Create a mock request
$request = new \Illuminate\Http\Request();
$request->merge(['school_id' => $student->school_id]);

echo "🔍 Testing delete method...\n";

try {
    $response = $controller->deleteStudent($request, $student->id);
    $responseData = $response->getData(true);
    
    echo "✅ Response received:\n";
    echo "  - Success: " . ($responseData['success'] ? 'true' : 'false') . "\n";
    echo "  - Message: " . $responseData['message'] . "\n";
    echo "  - Status Code: " . $response->getStatusCode() . "\n";
    
    // Check if student still exists
    $studentExists = DB::table('students')->where('id', $student->id)->exists();
    echo "  - Student still exists: " . ($studentExists ? 'YES' : 'NO') . "\n";
    
    if (!$studentExists) {
        echo "🎉 Student successfully deleted!\n";
    } else {
        echo "❌ Student was not deleted\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error testing delete: " . $e->getMessage() . "\n";
    echo "  - File: " . $e->getFile() . "\n";
    echo "  - Line: " . $e->getLine() . "\n";
}

echo "\n📊 Current student count: " . DB::table('students')->count() . "\n";
