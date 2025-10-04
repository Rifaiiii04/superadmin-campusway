<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING EDUCATION LEVEL LOGIC ===\n\n";

try {
    $majors = \App\Models\MajorRecommendation::with(['majorSubjectMappings.subject'])
        ->where('is_active', true)
        ->limit(3)
        ->get();
    
    foreach ($majors as $major) {
        echo "Major: {$major->major_name}\n";
        echo "Rumpun Ilmu: {$major->rumpun_ilmu}\n";
        
        // Test SuperAdmin logic
        $smaRumpun = ['ILMU ALAM', 'ILMU SOSIAL', 'ILMU BUDAYA', 'ILMU TERAPAN', 'ILMU FORMAL'];
        $educationLevel = in_array($major->rumpun_ilmu, $smaRumpun) ? 'SMA/MA' : 'SMK/MAK';
        echo "Education Level: {$educationLevel}\n";
        
        // Get optional subjects based on education level
        if ($educationLevel === 'SMK/MAK') {
            echo "Using SMK logic\n";
            $optionalSubjects = \App\Helpers\SMKSubjectHelper::getSubjectsForMajor($major->major_name);
        } else {
            echo "Using SMA logic\n";
            $optionalSubjects = $major->majorSubjectMappings
                ->filter(function($mapping) {
                    return $mapping->subject && 
                           $mapping->mapping_type === 'pilihan';
                })
                ->pluck('subject.name')
                ->toArray();
        }
        
        echo "Optional subjects: " . implode(', ', $optionalSubjects) . "\n";
        echo "Mapping count: " . $major->majorSubjectMappings->count() . "\n";
        
        // Show all mappings
        echo "All mappings:\n";
        foreach ($major->majorSubjectMappings as $mapping) {
            echo "  - Subject: " . ($mapping->subject ? $mapping->subject->name : 'NULL') . 
                 ", Type: {$mapping->mapping_type}, Education Level: {$mapping->education_level}\n";
        }
        echo "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

?>
