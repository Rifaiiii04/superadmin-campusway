<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking Student Choices Table Structure...\n\n";

// Check if student_choices table exists
$tableExists = DB::getSchemaBuilder()->hasTable('student_choices');
echo "📊 Table 'student_choices' exists: " . ($tableExists ? 'YES' : 'NO') . "\n";

if ($tableExists) {
    // Get table structure
    $columns = DB::getSchemaBuilder()->getColumnListing('student_choices');
    echo "\n📋 Table Structure:\n";
    foreach ($columns as $column) {
        echo "  - {$column}\n";
    }
    
    // Check data count
    $count = DB::table('student_choices')->count();
    echo "\n📊 Total Records: {$count}\n";
    
    if ($count > 0) {
        // Show sample data
        $sample = DB::table('student_choices')->first();
        echo "\n📄 Sample Record:\n";
        foreach ($sample as $key => $value) {
            echo "  - {$key}: {$value}\n";
        }
    }
} else {
    echo "❌ Table 'student_choices' does not exist!\n";
}

// Check if there are any related tables
echo "\n🔍 Checking for related tables...\n";
$tables = DB::getSchemaBuilder()->getAllTables();
$relatedTables = array_filter($tables, function($table) {
    return strpos($table, 'student') !== false || strpos($table, 'choice') !== false;
});

foreach ($relatedTables as $table) {
    echo "  - {$table}\n";
}
