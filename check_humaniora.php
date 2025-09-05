<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking HUMANIORA Program Studi...\n";

try {
    $programs = DB::table('program_studi')
        ->join('rumpun_ilmu', 'program_studi.rumpun_ilmu_id', '=', 'rumpun_ilmu.id')
        ->where('rumpun_ilmu.name', 'HUMANIORA')
        ->select('program_studi.name')
        ->get();
    
    echo "✅ HUMANIORA Program Studi (" . $programs->count() . "):\n";
    foreach($programs as $p) {
        echo "  - " . $p->name . "\n";
    }
} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✅ Check completed!\n";
