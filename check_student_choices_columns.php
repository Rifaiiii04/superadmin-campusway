<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking Student Choices Table Columns...\n\n";

// Get table structure
$columns = DB::getSchemaBuilder()->getColumnListing('student_choices');
echo "📋 Table Structure:\n";
foreach ($columns as $column) {
    echo "  - {$column}\n";
}

// Check if table has any data
$count = DB::table('student_choices')->count();
echo "\n📊 Total Records: {$count}\n";

if ($count > 0) {
    $sample = DB::table('student_choices')->first();
    echo "\n📄 Sample Record:\n";
    foreach ($sample as $key => $value) {
        echo "  - {$key}: {$value}\n";
    }
}
