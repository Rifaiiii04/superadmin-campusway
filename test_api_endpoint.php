<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Major Recommendations API endpoint...\n\n";

try {
    // Simulate the controller method
    $majorRecommendations = \App\Models\MajorRecommendation::with(['majorSubjectMappings.subject'])
        ->orderBy('major_name')
        ->get()
        ->map(function($major) {
            // Tentukan education level berdasarkan rumpun ilmu dan nama jurusan
            $educationLevel = 'SMA/MA'; // Simplified for testing
            
            // Dapatkan mata pelajaran wajib (3 untuk semua)
            $mandatorySubjects = \App\Models\Subject::where('subject_type', 'wajib')
                ->where('education_level', $educationLevel)
                ->pluck('name')
                ->toArray();
            
            // Dapatkan mata pelajaran pilihan berdasarkan education level
            $optionalSubjects = $major->majorSubjectMappings
                ->filter(function($mapping) {
                    return $mapping->subject && 
                           $mapping->mapping_type === 'pilihan';
                })
                ->pluck('subject.name')
                ->toArray();
            
            return [
                'id' => $major->id,
                'major_name' => $major->major_name,
                'description' => $major->description,
                'rumpun_ilmu' => $major->rumpun_ilmu,
                'education_level' => $educationLevel,
                'mandatory_subjects' => $mandatorySubjects,
                'optional_subjects' => $optionalSubjects,
                'is_active' => $major->is_active,
            ];
        });
    
    echo "Total majors: " . $majorRecommendations->count() . "\n\n";
    
    // Show first 3 majors with their optional subjects
    foreach($majorRecommendations->take(3) as $major) {
        echo "Major: {$major['major_name']}\n";
        echo "Mandatory subjects: " . implode(', ', $major['mandatory_subjects']) . "\n";
        echo "Optional subjects: " . implode(', ', $major['optional_subjects']) . "\n";
        echo "Optional count: " . count($major['optional_subjects']) . "\n\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

?>
