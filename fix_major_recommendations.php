<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Fixing major_recommendations to match program_studi...\n\n";

// Get all valid majors from program_studi
$validMajors = DB::table('program_studi')->pluck('name')->toArray();
echo "📊 Valid majors from program_studi: " . count($validMajors) . "\n";

// Get current major_recommendations
$currentRecommendations = DB::table('major_recommendations')->get();
echo "📊 Current major_recommendations: " . $currentRecommendations->count() . "\n\n";

// Check which recommendations are valid and which are invalid
$validRecommendations = [];
$invalidRecommendations = [];
$missingRecommendations = [];

foreach ($currentRecommendations as $recommendation) {
    $name = $recommendation->major_name;
    
    if (in_array($name, $validMajors)) {
        $validRecommendations[] = $name;
        echo "✅ Valid recommendation: {$name}\n";
    } else {
        $invalidRecommendations[] = ['id' => $recommendation->id, 'name' => $name];
        echo "❌ Invalid recommendation: {$name}\n";
    }
}

// Check which valid majors are missing from recommendations
foreach ($validMajors as $majorName) {
    if (!in_array($majorName, $validRecommendations)) {
        $missingRecommendations[] = $majorName;
        echo "➕ Missing recommendation: {$majorName}\n";
    }
}

echo "\n📊 Summary:\n";
echo "✅ Valid recommendations: " . count($validRecommendations) . "\n";
echo "❌ Invalid recommendations: " . count($invalidRecommendations) . "\n";
echo "➕ Missing recommendations: " . count($missingRecommendations) . "\n";

// Ask for confirmation
echo "\n🤔 Do you want to proceed with the cleanup? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim($line) === 'y' || trim($line) === 'Y') {
    echo "\n🚀 Proceeding with cleanup...\n";
    
    // Delete invalid recommendations
    if (!empty($invalidRecommendations)) {
        echo "\n🗑️ Deleting invalid recommendations...\n";
        foreach ($invalidRecommendations as $recommendation) {
            DB::table('major_recommendations')
                ->where('id', $recommendation['id'])
                ->delete();
            
            echo "  ❌ Deleted: {$recommendation['name']}\n";
        }
    }
    
    // Add missing recommendations
    if (!empty($missingRecommendations)) {
        echo "\n➕ Adding missing recommendations...\n";
        
        // Get rumpun ilmu mapping
        $rumpunMapping = DB::table('program_studi')
            ->join('rumpun_ilmu', 'program_studi.rumpun_ilmu_id', '=', 'rumpun_ilmu.id')
            ->pluck('rumpun_ilmu.name', 'program_studi.name')
            ->toArray();
        
        foreach ($missingRecommendations as $majorName) {
            $rumpunIlmu = $rumpunMapping[$majorName] ?? 'ILMU TERAPAN';
            
            // Ensure rumpun_ilmu is valid
            $validRumpun = ['HUMANIORA', 'ILMU SOSIAL', 'ILMU ALAM', 'ILMU FORMAL', 'ILMU TERAPAN'];
            if (!in_array($rumpunIlmu, $validRumpun)) {
                $rumpunIlmu = 'ILMU TERAPAN';
            }
            
            DB::table('major_recommendations')->insert([
                'major_name' => $majorName,
                'rumpun_ilmu' => $rumpunIlmu,
                'description' => "Program studi yang mempelajari {$majorName}",
                'career_prospects' => "Prospek karir yang menjanjikan di bidang {$majorName}",
                'required_subjects' => json_encode([]),
                'preferred_subjects' => json_encode([]),
                'kurikulum_merdeka_subjects' => json_encode([]),
                'kurikulum_2013_ipa_subjects' => json_encode([]),
                'kurikulum_2013_ips_subjects' => json_encode([]),
                'kurikulum_2013_bahasa_subjects' => json_encode([]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "  ✅ Added: {$majorName} ({$rumpunIlmu})\n";
        }
    }
    
    echo "\n✅ Cleanup completed!\n";
    
    // Final count
    $finalCount = DB::table('major_recommendations')->count();
    echo "📊 Final count: {$finalCount} recommendations\n";
    
    // Count by rumpun ilmu
    $rumpunCounts = DB::table('major_recommendations')
        ->select('rumpun_ilmu', DB::raw('count(*) as count'))
        ->groupBy('rumpun_ilmu')
        ->get();
    
    echo "\n📚 Final count by Rumpun Ilmu:\n";
    foreach ($rumpunCounts as $rumpun) {
        echo "  - {$rumpun->rumpun_ilmu}: {$rumpun->count} recommendations\n";
    }
    
} else {
    echo "\n❌ Operation cancelled.\n";
}

echo "\n📋 Expected final counts:\n";
echo "  - HUMANIORA: 5\n";
echo "  - ILMU SOSIAL: 4\n";
echo "  - ILMU ALAM: 7\n";
echo "  - ILMU FORMAL: 3\n";
echo "  - ILMU TERAPAN: 40\n";
echo "  - Total: 59\n";
