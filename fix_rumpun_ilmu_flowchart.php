<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔄 Fixing Rumpun Ilmu according to flowchart...\n\n";

// Clear existing rumpun ilmu
DB::table('rumpun_ilmu')->delete();
echo "✅ Cleared existing rumpun ilmu\n";

// Insert only the 3 rumpun ilmu from flowchart
$rumpunIlmu = [
    [
        'name' => 'HUMANIORA',
        'description' => 'Rumpun ilmu yang mempelajari manusia, budaya, dan ekspresi manusia',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'ILMU SOSIAL',
        'description' => 'Rumpun ilmu yang mempelajari perilaku manusia dalam masyarakat',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'ILMU ALAM',
        'description' => 'Rumpun ilmu yang mempelajari fenomena alam dan hukum-hukum alam',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

DB::table('rumpun_ilmu')->insert($rumpunIlmu);
echo "✅ Inserted " . count($rumpunIlmu) . " rumpun ilmu\n";

// Verify
$rumpun = DB::table('rumpun_ilmu')->get();

echo "\n📚 Rumpun Ilmu (after fix):\n";
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

echo "\n✅ Rumpun Ilmu fixed according to flowchart!\n";
