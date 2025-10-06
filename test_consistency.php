<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING DATA CONSISTENCY ===\n\n";

try {
    // Test 1: Check available subjects from database
    echo "1. Available subjects from database:\n";
    $availableSubjects = \App\Models\Subject::where('is_active', true)
        ->orderBy('name')
        ->pluck('name')
        ->toArray();
    
    echo "   Total subjects: " . count($availableSubjects) . "\n";
    echo "   Sample subjects: " . implode(', ', array_slice($availableSubjects, 0, 5)) . "...\n\n";

    // Test 2: Check major recommendations with optional subjects
    echo "2. Major recommendations with optional subjects:\n";
    $majors = \App\Models\MajorRecommendation::with(['majorSubjectMappings.subject'])
        ->where('is_active', true)
        ->limit(3)
        ->get();
    
    foreach ($majors as $major) {
        $optionalSubjects = $major->majorSubjectMappings
            ->filter(function($mapping) {
                return $mapping->subject && 
                       $mapping->mapping_type === 'pilihan';
            })
            ->pluck('subject.name')
            ->toArray();
        
        echo "   {$major->major_name}: " . implode(', ', $optionalSubjects) . "\n";
    }
    echo "\n";

    // Test 3: Check if subjects in mapping exist in available subjects
    echo "3. Checking subject consistency:\n";
    $mappingSubjects = \App\Models\MajorSubjectMapping::with('subject')
        ->get()
        ->pluck('subject.name')
        ->filter()
        ->unique()
        ->values()
        ->toArray();
    
    $missingSubjects = array_diff($mappingSubjects, $availableSubjects);
    $extraSubjects = array_diff($availableSubjects, $mappingSubjects);
    
    echo "   Subjects in mapping: " . count($mappingSubjects) . "\n";
    echo "   Available subjects: " . count($availableSubjects) . "\n";
    echo "   Missing from available: " . count($missingSubjects) . "\n";
    echo "   Extra in available: " . count($extraSubjects) . "\n";
    
    if (!empty($missingSubjects)) {
        echo "   Missing subjects: " . implode(', ', $missingSubjects) . "\n";
    }
    if (!empty($extraSubjects)) {
        echo "   Extra subjects: " . implode(', ', array_slice($extraSubjects, 0, 5)) . "...\n";
    }
    echo "\n";

    echo "✅ Consistency test completed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>
