<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking TKA Schedules table structure...\n\n";

try {
    $columns = DB::select("SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
                          FROM INFORMATION_SCHEMA.COLUMNS 
                          WHERE TABLE_NAME = 'tka_schedules' 
                          ORDER BY ORDINAL_POSITION");
    
    echo "📊 Table 'tka_schedules' columns:\n";
    foreach ($columns as $col) {
        echo "  - {$col->COLUMN_NAME} ({$col->DATA_TYPE}) - Nullable: {$col->IS_NULLABLE}\n";
    }
    
    echo "\n📋 Sample data from existing schedule:\n";
    $sample = DB::table('tka_schedules')->first();
    if ($sample) {
        foreach ($sample as $key => $value) {
            echo "  - {$key}: {$value}\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
