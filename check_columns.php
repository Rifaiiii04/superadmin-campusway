<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking subjects table columns...\n";

$columns = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'subjects' ORDER BY ORDINAL_POSITION");

echo "📋 Columns in subjects table:\n";
foreach($columns as $col) {
    echo "  - " . $col->COLUMN_NAME . "\n";
}

echo "\n✅ Column check completed!\n";
