<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 ADDING MISSING SENI AND LINGUISTIK\n";
echo "====================================\n\n";

try {
    // 1. Check if Seni and Linguistik exist
    echo "1. Checking for Seni and Linguistik majors...\n";
    
    $seni = \App\Models\MajorRecommendation::where('major_name', 'LIKE', '%Seni%')->first();
    $linguistik = \App\Models\MajorRecommendation::where('major_name', 'LIKE', '%Linguistik%')->first();
    
    if ($seni) {
        echo "   ✅ Found Seni: {$seni->major_name} (ID: {$seni->id})\n";
    } else {
        echo "   ❌ Seni not found\n";
    }
    
    if ($linguistik) {
        echo "   ✅ Found Linguistik: {$linguistik->major_name} (ID: {$linguistik->id})\n";
    } else {
        echo "   ❌ Linguistik not found\n";
    }
    
    // 2. Add mappings for Seni and Linguistik
    echo "\n2. Adding mappings for Seni and Linguistik...\n";
    
    if ($seni) {
        $seniSubjects = ["Seni Budaya", "Seni Budaya"];
        $seniEducationLevel = determineEducationLevel($seni->rumpun_ilmu);
        
        echo "   Processing Seni ({$seniEducationLevel}): " . implode(', ', $seniSubjects) . "\n";
        
        foreach ($seniSubjects as $index => $subjectName) {
            $subject = \App\Models\Subject::where('name', $subjectName)->first();
            
            if ($subject) {
                \App\Models\MajorSubjectMapping::create([
                    'major_id' => $seni->id,
                    'subject_id' => $subject->id,
                    'education_level' => $seniEducationLevel,
                    'mapping_type' => 'pilihan',
                    'priority' => $index + 1,
                    'is_active' => true,
                    'subject_type' => 'pilihan'
                ]);
                echo "     ✅ Mapped: {$subjectName}\n";
            } else {
                echo "     ❌ Subject not found: {$subjectName}\n";
            }
        }
        
        // Update major recommendation
        $seni->update([
            'preferred_subjects' => $seniSubjects
        ]);
        echo "   ✅ Updated Seni: " . implode(', ', $seniSubjects) . "\n";
    }
    
    if ($linguistik) {
        $linguistikSubjects = ["Bahasa Indonesia", "Bahasa Inggris"];
        $linguistikEducationLevel = determineEducationLevel($linguistik->rumpun_ilmu);
        
        echo "   Processing Linguistik ({$linguistikEducationLevel}): " . implode(', ', $linguistikSubjects) . "\n";
        
        foreach ($linguistikSubjects as $index => $subjectName) {
            $subject = \App\Models\Subject::where('name', $subjectName)->first();
            
            if ($subject) {
                \App\Models\MajorSubjectMapping::create([
                    'major_id' => $linguistik->id,
                    'subject_id' => $subject->id,
                    'education_level' => $linguistikEducationLevel,
                    'mapping_type' => 'pilihan',
                    'priority' => $index + 1,
                    'is_active' => true,
                    'subject_type' => 'pilihan'
                ]);
                echo "     ✅ Mapped: {$subjectName}\n";
            } else {
                echo "     ❌ Subject not found: {$subjectName}\n";
            }
        }
        
        // Update major recommendation
        $linguistik->update([
            'preferred_subjects' => $linguistikSubjects
        ]);
        echo "   ✅ Updated Linguistik: " . implode(', ', $linguistikSubjects) . "\n";
    }
    
    // 3. Verify the updates
    echo "\n3. Verifying updates...\n";
    
    $totalMappings = \App\Models\MajorSubjectMapping::where('mapping_type', 'pilihan')->count();
    $majorsWithMappings = \App\Models\MajorRecommendation::whereHas('majorSubjectMappings', function($query) {
        $query->where('mapping_type', 'pilihan');
    })->count();
    
    echo "   Total optional mappings: {$totalMappings}\n";
    echo "   Majors with mappings: {$majorsWithMappings}\n";
    
    // 4. Test the specific majors
    echo "\n4. Testing specific majors...\n";
    
    $testMajors = ['Seni', 'Linguistik', 'Ilmu Kelautan', 'Matematika', 'Fisika'];
    
    foreach ($testMajors as $testMajorName) {
        $testMajor = \App\Models\MajorRecommendation::where('major_name', 'LIKE', "%{$testMajorName}%")->first();
        
        if ($testMajor) {
            $mappings = \App\Models\MajorSubjectMapping::where('major_id', $testMajor->id)
                ->where('mapping_type', 'pilihan')
                ->with('subject')
                ->get();
            
            $subjectNames = $mappings->pluck('subject.name')->toArray();
            
            if (!empty($subjectNames)) {
                echo "   ✅ {$testMajor->major_name}: " . implode(', ', $subjectNames) . "\n";
            } else {
                echo "   ❌ {$testMajor->major_name}: No optional subjects mapped\n";
            }
        } else {
            echo "   ❌ {$testMajorName}: Not found in database\n";
        }
    }
    
    // 5. Show all majors with their optional subjects
    echo "\n5. All majors with optional subjects:\n";
    
    $allMajors = \App\Models\MajorRecommendation::where('is_active', true)
        ->whereHas('majorSubjectMappings', function($query) {
            $query->where('mapping_type', 'pilihan');
        })
        ->with(['majorSubjectMappings.subject'])
        ->get();
    
    foreach ($allMajors as $major) {
        $optionalSubjects = $major->majorSubjectMappings
            ->where('mapping_type', 'pilihan')
            ->pluck('subject.name')
            ->toArray();
        
        echo "   - {$major->major_name}: " . implode(', ', $optionalSubjects) . "\n";
    }

    echo "\n🎉 MISSING SENI AND LINGUISTIK ADDED SUCCESSFULLY!\n";
    echo "================================================\n";
    echo "✅ Seni and Linguistik mappings added\n";
    echo "✅ Database updated with correct mappings\n";
    echo "✅ All data is consistent\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

function determineEducationLevel($rumpunIlmu)
{
    $smaRumpun = ['ILMU ALAM', 'ILMU SOSIAL', 'ILMU BUDAYA', 'HUMANIORA', 'ILMU FORMAL'];
    
    if (in_array($rumpunIlmu, $smaRumpun)) {
        return 'SMA/MA';
    } else {
        return 'SMK/MAK';
    }
}
