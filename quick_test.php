<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== QUICK API TEST ===\n\n";

try {
    // Test database mapping
    echo "1. Database mapping test:\n";
    $mappings = DB::table('major_subject_mappings')
        ->join('major_recommendations', 'major_subject_mappings.major_id', '=', 'major_recommendations.id')
        ->join('subjects', 'major_subject_mappings.subject_id', '=', 'subjects.id')
        ->where('major_recommendations.major_name', 'Matematika')
        ->where('major_subject_mappings.education_level', 'SMA/MA')
        ->where('major_subject_mappings.mapping_type', 'pilihan')
        ->select('subjects.name', 'major_subject_mappings.priority')
        ->orderBy('major_subject_mappings.priority')
        ->get();
    
    echo "   Mappings for Matematika major:\n";
    foreach ($mappings as $mapping) {
        echo "   - {$mapping->name} (priority: {$mapping->priority})\n";
    }
    echo "\n";

    // Test SuperAdmin logic
    echo "2. SuperAdmin logic test:\n";
    $major = DB::table('major_recommendations')->where('major_name', 'Matematika')->first();
    if ($major) {
        $majorSubjectMappings = DB::table('major_subject_mappings')
            ->join('subjects', 'major_subject_mappings.subject_id', '=', 'subjects.id')
            ->where('major_subject_mappings.major_id', $major->id)
            ->where('major_subject_mappings.education_level', 'SMA/MA')
            ->where('major_subject_mappings.mapping_type', 'pilihan')
            ->select('subjects.name')
            ->get();
        
        echo "   Optional subjects for Matematika:\n";
        foreach ($majorSubjectMappings as $mapping) {
            echo "   - {$mapping->name}\n";
        }
    }
    echo "\n";

    echo "✅ Test completed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>
