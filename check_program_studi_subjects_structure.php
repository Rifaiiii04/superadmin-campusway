<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking program_studi_subjects table structure...\n";

try {
    $columns = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'program_studi_subjects'");
    
    echo "📋 Columns in program_studi_subjects table:\n";
    foreach($columns as $col) {
        echo "  - " . $col->COLUMN_NAME . "\n";
    }
    
    echo "\n📊 Sample data:\n";
    $sample = DB::table('program_studi_subjects')->first();
    if ($sample) {
        foreach($sample as $key => $value) {
            echo "  {$key}: {$value}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🔍 Checking subjects table for Seni Budaya...\n";
$subjects = DB::table('subjects')->where('name', 'like', '%Seni%')->get();
foreach($subjects as $subject) {
    echo "  - {$subject->name} (ID: {$subject->id})\n";
}

echo "\n✅ Structure check completed!\n";
