<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking valid type values...\n";

$subjects = DB::table('subjects')->select('type')->distinct()->get();
foreach($subjects as $subject) {
    echo "  - " . $subject->type . "\n";
}

echo "\n✅ Type values check completed!\n";
