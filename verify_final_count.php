<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Verifying final count matches Pusmendik table...\n\n";

// Check program_studi count
$programStudiCount = DB::table('program_studi')->count();
echo "📊 program_studi count: {$programStudiCount}\n";

// Check major_recommendations count
$majorRecommendationsCount = DB::table('major_recommendations')->count();
echo "📊 major_recommendations count: {$majorRecommendationsCount}\n";

// Check by rumpun ilmu
echo "\n📚 Count by Rumpun Ilmu:\n";

// program_studi
$programStudiRumpun = DB::table('program_studi')
    ->join('rumpun_ilmu', 'program_studi.rumpun_ilmu_id', '=', 'rumpun_ilmu.id')
    ->select('rumpun_ilmu.name', DB::raw('count(*) as count'))
    ->groupBy('rumpun_ilmu.name')
    ->orderBy('rumpun_ilmu.name')
    ->get();

echo "program_studi:\n";
foreach ($programStudiRumpun as $rumpun) {
    echo "  - {$rumpun->name}: {$rumpun->count}\n";
}

// major_recommendations
$majorRecommendationsRumpun = DB::table('major_recommendations')
    ->select('rumpun_ilmu', DB::raw('count(*) as count'))
    ->groupBy('rumpun_ilmu')
    ->orderBy('rumpun_ilmu')
    ->get();

echo "\nmajor_recommendations:\n";
foreach ($majorRecommendationsRumpun as $rumpun) {
    echo "  - {$rumpun->rumpun_ilmu}: {$rumpun->count}\n";
}

// Expected counts
$expectedCounts = [
    'HUMANIORA' => 5,
    'ILMU SOSIAL' => 4,
    'ILMU ALAM' => 7,
    'ILMU FORMAL' => 3,
    'ILMU TERAPAN' => 40
];

echo "\n📋 Expected counts:\n";
foreach ($expectedCounts as $rumpun => $expected) {
    echo "  - {$rumpun}: {$expected}\n";
}

echo "\nTotal expected: 59\n";

// Check if counts match
$allMatch = true;
foreach ($expectedCounts as $rumpun => $expected) {
    $programStudiCount = $programStudiRumpun->where('name', $rumpun)->first()->count ?? 0;
    $majorRecommendationsCount = $majorRecommendationsRumpun->where('rumpun_ilmu', $rumpun)->first()->count ?? 0;
    
    if ($programStudiCount !== $expected || $majorRecommendationsCount !== $expected) {
        $allMatch = false;
        echo "\n❌ Mismatch for {$rumpun}:\n";
        echo "  - program_studi: {$programStudiCount} (expected: {$expected})\n";
        echo "  - major_recommendations: {$majorRecommendationsCount} (expected: {$expected})\n";
    } else {
        echo "\n✅ Match for {$rumpun}: {$programStudiCount} = {$expected}\n";
    }
}

if ($allMatch) {
    echo "\n✅ All counts match Pusmendik table!\n";
    echo "🎉 Dashboard should now show 59 total majors\n";
} else {
    echo "\n❌ Some counts don't match\n";
}

echo "\n📊 Summary:\n";
echo "  - program_studi: {$programStudiCount} majors\n";
echo "  - major_recommendations: {$majorRecommendationsCount} majors\n";
echo "  - Expected: 59 majors\n";
echo "  - Status: " . ($programStudiCount === 59 && $majorRecommendationsCount === 59 ? "✅ CORRECT" : "❌ INCORRECT") . "\n";
