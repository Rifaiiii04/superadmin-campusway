<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Simple verification...\n\n";

// Check program_studi count
$programStudiCount = DB::table('program_studi')->count();
echo "📊 program_studi count: {$programStudiCount}\n";

// Check major_recommendations count
$majorRecommendationsCount = DB::table('major_recommendations')->count();
echo "📊 major_recommendations count: {$majorRecommendationsCount}\n";

// Check by rumpun ilmu
echo "\n📚 Count by Rumpun Ilmu:\n";

$rumpunCounts = DB::table('major_recommendations')
    ->select('rumpun_ilmu', DB::raw('count(*) as count'))
    ->groupBy('rumpun_ilmu')
    ->orderBy('rumpun_ilmu')
    ->get();

foreach ($rumpunCounts as $rumpun) {
    echo "  - {$rumpun->rumpun_ilmu}: {$rumpun->count}\n";
}

$total = $rumpunCounts->sum('count');
echo "\n📊 Total: {$total}\n";

if ($total === 59) {
    echo "✅ CORRECT! Dashboard should now show 59 total majors\n";
} else {
    echo "❌ INCORRECT! Expected 59, got {$total}\n";
}
