<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing add student validation...\n\n";

// Test different scenarios
$testCases = [
    [
        'name' => 'Valid data',
        'data' => [
            'nisn' => '1111111111',
            'name' => 'Test Student 1',
            'kelas' => 'X IPA 1',
            'email' => 'test1@example.com',
            'phone' => '081234567890',
            'parent_phone' => '081234567891',
            'password' => 'password123',
            'school_id' => 4
        ]
    ],
    [
        'name' => 'Missing school_id',
        'data' => [
            'nisn' => '2222222222',
            'name' => 'Test Student 2',
            'kelas' => 'X IPA 1',
            'email' => 'test2@example.com',
            'phone' => '081234567890',
            'parent_phone' => '081234567891',
            'password' => 'password123',
            // school_id missing
        ]
    ],
    [
        'name' => 'Invalid school_id',
        'data' => [
            'nisn' => '3333333333',
            'name' => 'Test Student 3',
            'kelas' => 'X IPA 1',
            'email' => 'test3@example.com',
            'phone' => '081234567890',
            'parent_phone' => '081234567891',
            'password' => 'password123',
            'school_id' => 999 // Invalid school ID
        ]
    ],
    [
        'name' => 'Short NISN',
        'data' => [
            'nisn' => '123456789', // 9 digits instead of 10
            'name' => 'Test Student 4',
            'kelas' => 'X IPA 1',
            'email' => 'test4@example.com',
            'phone' => '081234567890',
            'parent_phone' => '081234567891',
            'password' => 'password123',
            'school_id' => 4
        ]
    ],
    [
        'name' => 'Short password',
        'data' => [
            'nisn' => '4444444444',
            'name' => 'Test Student 5',
            'kelas' => 'X IPA 1',
            'email' => 'test5@example.com',
            'phone' => '081234567890',
            'parent_phone' => '081234567891',
            'password' => '123', // Too short
            'school_id' => 4
        ]
    ]
];

foreach ($testCases as $testCase) {
    echo "🔍 Testing: {$testCase['name']}\n";
    echo "📊 Data: " . json_encode($testCase['data'], JSON_PRETTY_PRINT) . "\n";
    
    try {
        $controller = new \App\Http\Controllers\SchoolDashboardController();
        $request = new \Illuminate\Http\Request();
        $request->merge($testCase['data']);
        
        $response = $controller->addStudent($request);
        $responseData = $response->getData(true);
        
        if ($response->getStatusCode() === 201) {
            echo "✅ Result: SUCCESS\n";
            echo "  - Status: {$response->getStatusCode()}\n";
            echo "  - Message: {$responseData['message']}\n";
        } else {
            echo "❌ Result: FAILED\n";
            echo "  - Status: {$response->getStatusCode()}\n";
            echo "  - Message: {$responseData['message']}\n";
        }
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        echo "❌ Result: VALIDATION ERROR\n";
        echo "  - Status: 422\n";
        echo "  - Errors:\n";
        foreach ($e->errors() as $field => $errors) {
            echo "    - {$field}: " . implode(', ', $errors) . "\n";
        }
    } catch (\Exception $e) {
        echo "❌ Result: ERROR\n";
        echo "  - Message: {$e->getMessage()}\n";
        echo "  - Type: " . get_class($e) . "\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}
