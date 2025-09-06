<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🗑️ Deleting duplicate NISN...\n\n";

$nisn = '2233445566';

// Check if NISN exists
$student = DB::table('students')->where('nisn', $nisn)->first();

if ($student) {
    echo "📊 Found student with NISN {$nisn}:\n";
    echo "  - ID: {$student->id}\n";
    echo "  - Name: {$student->name}\n";
    echo "  - NISN: {$student->nisn}\n";
    echo "  - School ID: {$student->school_id}\n\n";
    
    // Delete the student
    DB::table('students')->where('nisn', $nisn)->delete();
    
    echo "✅ Student deleted successfully!\n";
} else {
    echo "❌ No student found with NISN {$nisn}\n";
}

echo "\n📊 Current student count: " . DB::table('students')->count() . "\n";
