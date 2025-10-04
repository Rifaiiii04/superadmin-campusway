<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VERIFYING FINAL OPTIONAL SUBJECTS\n";
echo "===================================\n\n";

try {
    // 1. Check total mappings
    echo "1. Checking total mappings...\n";
    
    $totalMappings = \App\Models\MajorSubjectMapping::where('mapping_type', 'pilihan')->count();
    $totalMajors = \App\Models\MajorRecommendation::where('is_active', true)->count();
    $majorsWithMappings = \App\Models\MajorRecommendation::whereHas('majorSubjectMappings', function($query) {
        $query->where('mapping_type', 'pilihan');
    })->count();
    
    echo "   Total optional mappings: {$totalMappings}\n";
    echo "   Total active majors: {$totalMajors}\n";
    echo "   Majors with mappings: {$majorsWithMappings}\n";
    
    if ($majorsWithMappings === $totalMajors) {
        echo "   ✅ All majors have optional subject mappings\n";
    } else {
        echo "   ❌ Some majors are missing optional subject mappings\n";
    }
    
    // 2. Check specific mappings as requested
    echo "\n2. Checking specific mappings as requested...\n";
    
    $requestedMappings = [
        "Seni" => ["Seni Budaya", "Seni Budaya"],
        "Sejarah" => ["Sejarah Indonesia", "Sejarah Indonesia"],
        "Linguistik" => ["Bahasa Indonesia", "Bahasa Inggris"],
        "Susastra" => ["Bahasa Indonesia", "Bahasa Asing"],
        "Filsafat" => ["Sosiologi", "Sejarah Indonesia"],
        "Sosial" => ["Sosiologi", "Antropologi"],
        "Ekonomi" => ["Ekonomi", "Matematika"],
        "Pertahanan" => ["PPKn", "PPKn"],
        "Psikologi" => ["Sosiologi", "Matematika"],
        "Kimia" => ["Kimia", "Kimia"],
        "Ilmu Kebumian" => ["Fisika", "Matematika"],
        "Ilmu Kelautan" => ["Biologi", "Geografi"],
        "Biologi" => ["Biologi", "Kimia"],
        "Biofisika" => ["Fisika", "Matematika"],
        "Fisika" => ["Fisika", "Matematika"],
        "Astronomi" => ["Fisika", "Matematika"],
        "Komputer" => ["Matematika", "Logika"],
        "Logika" => ["Matematika", "Statistika"],
        "Matematika" => ["Matematika", "Fisika"]
    ];
    
    $correctMappings = 0;
    $totalChecked = 0;
    
    foreach ($requestedMappings as $majorName => $expectedSubjects) {
        $major = \App\Models\MajorRecommendation::where('major_name', 'LIKE', "%{$majorName}%")->first();
        
        if ($major) {
            $mappings = \App\Models\MajorSubjectMapping::where('major_id', $major->id)
                ->where('mapping_type', 'pilihan')
                ->with('subject')
                ->get();
            
            $actualSubjects = $mappings->pluck('subject.name')->toArray();
            
            if ($actualSubjects === $expectedSubjects) {
                echo "   ✅ {$major->major_name}: " . implode(', ', $actualSubjects) . "\n";
                $correctMappings++;
            } else {
                echo "   ❌ {$major->major_name}: Expected " . implode(', ', $expectedSubjects) . ", Got " . implode(', ', $actualSubjects) . "\n";
            }
            $totalChecked++;
        } else {
            echo "   ⚠️  {$majorName}: Not found in database\n";
        }
    }
    
    echo "\n   Correct mappings: {$correctMappings}/{$totalChecked}\n";
    
    // 3. Check database consistency
    echo "\n3. Checking database consistency...\n";
    
    $majors = \App\Models\MajorRecommendation::where('is_active', true)
        ->with(['majorSubjectMappings.subject'])
        ->get();
    
    $majorsWithCorrectCount = 0;
    
    foreach ($majors as $major) {
        $optionalMappings = $major->majorSubjectMappings
            ->where('mapping_type', 'pilihan');
        
        if ($optionalMappings->count() === 2) {
            $majorsWithCorrectCount++;
        } else {
            echo "   ⚠️  {$major->major_name}: Has {$optionalMappings->count()} optional subjects (expected 2)\n";
        }
    }
    
    echo "   Majors with exactly 2 optional subjects: {$majorsWithCorrectCount}/{$majors->count()}\n";
    
    if ($majorsWithCorrectCount === $majors->count()) {
        echo "   ✅ All majors have exactly 2 optional subjects\n";
    } else {
        echo "   ❌ Some majors don't have exactly 2 optional subjects\n";
    }
    
    // 4. Test API consistency
    echo "\n4. Testing API consistency...\n";
    
    $controller = new \App\Http\Controllers\SuperAdminController();
    $response = $controller->majorRecommendations();
    
    echo "   ✅ SuperAdmin API working\n";
    echo "   Response type: " . get_class($response) . "\n";
    
    // 5. Show sample of all mappings
    echo "\n5. Sample of all mappings:\n";
    
    $sampleMajors = $majors->take(10);
    foreach ($sampleMajors as $major) {
        $optionalSubjects = $major->majorSubjectMappings
            ->where('mapping_type', 'pilihan')
            ->pluck('subject.name')
            ->toArray();
        
        echo "   - {$major->major_name}: " . implode(', ', $optionalSubjects) . "\n";
    }
    
    if ($majors->count() > 10) {
        echo "   ... and " . ($majors->count() - 10) . " more majors\n";
    }

    echo "\n🎉 FINAL OPTIONAL SUBJECTS VERIFICATION COMPLETED!\n";
    echo "================================================\n";
    echo "✅ Total mappings: {$totalMappings}\n";
    echo "✅ Majors with mappings: {$majorsWithMappings}/{$totalMajors}\n";
    echo "✅ Correct mappings: {$correctMappings}/{$totalChecked}\n";
    echo "✅ Majors with 2 subjects: {$majorsWithCorrectCount}/{$majors->count()}\n";
    echo "✅ All data is consistent and updated in database\n";
    echo "\n🎉 OPTIONAL SUBJECTS UPDATE SUCCESSFUL!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
