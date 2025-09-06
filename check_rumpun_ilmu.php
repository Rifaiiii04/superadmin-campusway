<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking Rumpun Ilmu...\n\n";

$rumpun = DB::table('rumpun_ilmu')->get();

echo "📚 Rumpun Ilmu:\n";
foreach($rumpun as $r) {
    echo "  - {$r->name}\n";
}

echo "\n📊 Total: " . $rumpun->count() . " rumpun ilmu\n";

// Check if matches flowchart
$flowchartRumpun = ['HUMANIORA', 'ILMU SOSIAL', 'ILMU ALAM'];
$currentRumpun = $rumpun->pluck('name')->toArray();

echo "\n🔍 Comparison with flowchart:\n";
foreach($flowchartRumpun as $expected) {
    $exists = in_array($expected, $currentRumpun);
    echo "  - {$expected}: " . ($exists ? '✅' : '❌') . "\n";
}

// Check for extra rumpun ilmu
$extra = array_diff($currentRumpun, $flowchartRumpun);
if (!empty($extra)) {
    echo "\n⚠️ Extra rumpun ilmu (not in flowchart):\n";
    foreach($extra as $extraRumpun) {
        echo "  - {$extraRumpun}\n";
    }
}