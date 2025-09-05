<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking database tables...\n";

$tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME");

echo "📋 Available tables:\n";
foreach($tables as $table) {
    echo "  - " . $table->TABLE_NAME . "\n";
}

echo "\n✅ Table check completed!\n";
