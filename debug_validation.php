<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Debugging validation...\n\n";

// Test with minimal data
$testData = [
    'nisn' => '5555555555',
    'name' => 'Debug Student',
    'kelas' => 'X IPA 1',
    'password' => 'password123',
    'school_id' => 4
];

echo "📊 Test data:\n";
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
$existingNisn = DB::table('students')->where('nisn', '5555555555')->first();
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
