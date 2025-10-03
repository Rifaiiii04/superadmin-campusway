<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECK MAPPING RESULTS ===\n\n";

try {
    // Total mappings
    $totalMappings = DB::table('major_subject_mappings')->count();
    echo "Total mappings: {$totalMappings}\n";
    
    // SMA mappings (pilihan)
    $smaMappings = DB::table('major_subject_mappings')
        ->where('education_level', 'SMA/MA')
        ->where('mapping_type', 'pilihan')
        ->count();
    echo "SMA/MA (pilihan): {$smaMappings}\n";
    
    // SMK mappings
    $smkMappings = DB::table('major_subject_mappings')
        ->where('education_level', 'SMK/MAK')
        ->count();
    echo "SMK/MAK (total): {$smkMappings}\n\n";
    
    // Sample data
    echo "Sample mappings:\n";
    $samples = DB::table('major_subject_mappings')
        ->join('major_recommendations', 'major_subject_mappings.major_id', '=', 'major_recommendations.id')
        ->join('subjects', 'major_subject_mappings.subject_id', '=', 'subjects.id')
        ->select('major_recommendations.major_name', 'subjects.name as subject_name', 'major_subject_mappings.education_level', 'major_subject_mappings.mapping_type')
        ->orderBy('major_recommendations.major_name')
        ->orderBy('major_subject_mappings.priority')
        ->limit(15)
        ->get();
    
    foreach ($samples as $mapping) {
        $type = $mapping->mapping_type === 'pilihan_wajib' ? 'wajib' : 'pilihan';
        echo "  {$mapping->major_name} ({$mapping->education_level}): {$mapping->subject_name} ({$type})\n";
    }
    
    // Check per major count
    echo "\nMapping count per major:\n";
    $majorCounts = DB::table('major_subject_mappings')
        ->join('major_recommendations', 'major_subject_mappings.major_id', '=', 'major_recommendations.id')
        ->select('major_recommendations.major_name', 'major_subject_mappings.education_level', DB::raw('COUNT(*) as count'))
        ->groupBy('major_recommendations.major_name', 'major_subject_mappings.education_level')
        ->orderBy('major_recommendations.major_name')
        ->limit(10)
        ->get();
    
    foreach ($majorCounts as $count) {
        echo "  {$count->major_name} ({$count->education_level}): {$count->count} subjects\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
