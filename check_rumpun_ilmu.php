<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking rumpun_ilmu data...\n";

try {
    $rumpun = DB::table('rumpun_ilmu')->get();
    echo "✅ rumpun_ilmu records: " . $rumpun->count() . "\n";
    foreach($rumpun as $r) {
        echo "  - " . $r->name . "\n";
    }
} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✅ Check completed!\n";
