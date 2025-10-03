<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing optional subjects retrieval...\n\n";

try {
    $majors = \App\Models\MajorRecommendation::with(['majorSubjectMappings.subject'])
        ->where('is_active', true)
        ->limit(5)
        ->get();
    
    foreach($majors as $major) {
        echo "Major: {$major->major_name}\n";
        
        $optionalSubjects = $major->majorSubjectMappings
            ->filter(function($mapping) {
                return $mapping->subject && 
                       $mapping->mapping_type === 'pilihan';
            })
            ->pluck('subject.name')
            ->toArray();
        
        echo "Optional subjects: " . implode(', ', $optionalSubjects) . "\n";
        echo "Count: " . count($optionalSubjects) . "\n\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
