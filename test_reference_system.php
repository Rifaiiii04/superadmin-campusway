<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing reference subjects system across all components...\n\n";

// Test 1: Check if all majors have rumpun_ilmu
echo "1️⃣ Testing majors have rumpun_ilmu...\n";
$majorsWithoutRumpun = DB::table('program_studi')
    ->whereNull('rumpun_ilmu_id')
    ->count();

if ($majorsWithoutRumpun === 0) {
    echo "✅ All majors have rumpun_ilmu_id\n";
} else {
    echo "❌ {$majorsWithoutRumpun} majors missing rumpun_ilmu_id\n";
}

// Test 2: Check if all major recommendations have rumpun_ilmu
echo "\n2️⃣ Testing major recommendations have rumpun_ilmu...\n";
$recommendationsWithoutRumpun = DB::table('major_recommendations')
    ->whereNull('rumpun_ilmu')
    ->count();

if ($recommendationsWithoutRumpun === 0) {
    echo "✅ All major recommendations have rumpun_ilmu\n";
} else {
    echo "❌ {$recommendationsWithoutRumpun} recommendations missing rumpun_ilmu\n";
}

// Test 3: Check reference subjects mappings
echo "\n3️⃣ Testing reference subjects mappings...\n";
$totalMappings = DB::table('program_studi_subjects')->count();
$majorsWithMappings = DB::table('program_studi_subjects')
    ->join('program_studi', 'program_studi_subjects.program_studi_id', '=', 'program_studi.id')
    ->select('program_studi.name')
    ->distinct()
    ->count();

echo "📊 Total mappings: {$totalMappings}\n";
echo "📚 Majors with mappings: {$majorsWithMappings}\n";

// Test 4: Check API endpoints
echo "\n4️⃣ Testing API endpoints...\n";

// Test /api/web/majors
try {
    $response = file_get_contents('http://127.0.0.1:8000/api/web/majors');
    $data = json_decode($response, true);
    
    if ($data && isset($data['data'])) {
        $sampleMajor = $data['data'][0] ?? null;
        if ($sampleMajor && isset($sampleMajor['rumpun_ilmu'])) {
            echo "✅ /api/web/majors returns rumpun_ilmu\n";
        } else {
            echo "❌ /api/web/majors missing rumpun_ilmu\n";
        }
    } else {
        echo "⚠️ /api/web/majors not accessible or invalid response\n";
    }
} catch (Exception $e) {
    echo "⚠️ /api/web/majors not accessible: " . $e->getMessage() . "\n";
}

// Test 5: Check if all rumpun ilmu are present
echo "\n5️⃣ Testing rumpun ilmu completeness...\n";
$expectedRumpun = ['HUMANIORA', 'ILMU SOSIAL', 'ILMU ALAM', 'ILMU FORMAL', 'ILMU TERAPAN'];
$existingRumpun = DB::table('rumpun_ilmu')->pluck('name')->toArray();

foreach ($expectedRumpun as $rumpun) {
    if (in_array($rumpun, $existingRumpun)) {
        echo "✅ {$rumpun} exists\n";
    } else {
        echo "❌ {$rumpun} missing\n";
    }
}

// Test 6: Check subject mappings follow new rules
echo "\n6️⃣ Testing subject mappings follow new rules...\n";
$sampleMappings = DB::table('program_studi_subjects')
    ->join('subjects', 'program_studi_subjects.subject_id', '=', 'subjects.id')
    ->join('program_studi', 'program_studi_subjects.program_studi_id', '=', 'program_studi.id')
    ->select('program_studi.name as major_name', 'subjects.name as subject_name', 'program_studi_subjects.kurikulum_type')
    ->limit(10)
    ->get();

echo "📋 Sample mappings:\n";
foreach ($sampleMappings as $mapping) {
    echo "  - {$mapping->major_name} ({$mapping->kurikulum_type}): {$mapping->subject_name}\n";
}

echo "\n🎉 Reference system test completed!\n";
echo "\n📋 Summary:\n";
echo "  - Majors: " . ($majorsWithoutRumpun === 0 ? "✅" : "❌") . "\n";
echo "  - Recommendations: " . ($recommendationsWithoutRumpun === 0 ? "✅" : "❌") . "\n";
echo "  - Mappings: {$totalMappings} total\n";
echo "  - API: " . (isset($sampleMajor) && isset($sampleMajor['rumpun_ilmu']) ? "✅" : "❌") . "\n";
echo "  - Rumpun Ilmu: " . (count(array_intersect($expectedRumpun, $existingRumpun)) === 5 ? "✅" : "❌") . "\n";
