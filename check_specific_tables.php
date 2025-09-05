<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking specific tables...\n";

// Check rumpun_ilmu
try {
    $rumpun = DB::table('rumpun_ilmu')->count();
    echo "✅ rumpun_ilmu: {$rumpun} records\n";
} catch(Exception $e) {
    echo "❌ rumpun_ilmu: NOT FOUND\n";
}

// Check program_studi
try {
    $program = DB::table('program_studi')->count();
    echo "✅ program_studi: {$program} records\n";
} catch(Exception $e) {
    echo "❌ program_studi: NOT FOUND\n";
}

// Check major_recommendations
try {
    $major = DB::table('major_recommendations')->count();
    echo "✅ major_recommendations: {$major} records\n";
} catch(Exception $e) {
    echo "❌ major_recommendations: NOT FOUND\n";
}

// Check subjects
try {
    $subjects = DB::table('subjects')->count();
    echo "✅ subjects: {$subjects} records\n";
} catch(Exception $e) {
    echo "❌ subjects: NOT FOUND\n";
}

echo "\n✅ Table check completed!\n";
