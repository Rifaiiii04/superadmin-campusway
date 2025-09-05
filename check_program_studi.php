<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking program_studi data...\n";

try {
    $program = DB::table('program_studi')->count();
    echo "✅ program_studi records: " . $program . "\n";
} catch(Exception $e) {
    echo "❌ program_studi: NOT FOUND\n";
}

echo "\n✅ Check completed!\n";
