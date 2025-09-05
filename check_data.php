<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking Major Recommendations Data...\n\n";

// Check rumpun_ilmu distribution
$summary = DB::table('major_recommendations')
    ->select('rumpun_ilmu', DB::raw('COUNT(*) as count'))
    ->groupBy('rumpun_ilmu')
    ->get();

echo "📊 Rumpun Ilmu Distribution:\n";
foreach ($summary as $item) {
    echo "  - {$item->rumpun_ilmu}: {$item->count} majors\n";
}

echo "\n📋 Sample Data:\n";
$samples = DB::table('major_recommendations')
    ->select('major_name', 'rumpun_ilmu')
    ->limit(10)
    ->get();

foreach ($samples as $sample) {
    echo "  - {$sample->major_name}: {$sample->rumpun_ilmu}\n";
}

echo "\n✅ Data check completed!\n";
