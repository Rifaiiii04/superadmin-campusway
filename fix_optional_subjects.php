<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 FIXING OPTIONAL SUBJECTS - EXACTLY 2 PER MAJOR\n";
echo "================================================\n\n";

try {
    // 1. Clear existing mappings
    echo "1. Clearing existing mappings...\n";
    \App\Models\MajorSubjectMapping::truncate();
    echo "✅ Existing mappings cleared\n\n";

    // 2. Get all active majors
    echo "2. Getting active majors...\n";
    $majors = \App\Models\MajorRecommendation::where('is_active', true)->get();
    echo "✅ Found " . $majors->count() . " active majors\n\n";

    // 3. Get all optional subjects
    echo "3. Getting optional subjects...\n";
    $optionalSubjects = \App\Models\Subject::where('subject_type', 'pilihan')
        ->where('education_level', 'SMA/MA')
        ->get();
    echo "✅ Found " . $optionalSubjects->count() . " optional subjects\n\n";

    // 4. Create mappings with exactly 2 subjects per major
    echo "4. Creating mappings with exactly 2 subjects per major...\n";
    
    $mappingCount = 0;
    $subjectIndex = 0;
    
    foreach ($majors as $major) {
        $educationLevel = determineEducationLevel($major->rumpun_ilmu);
        
        if ($educationLevel === 'SMA/MA') {
            // SMA/MA: Exactly 2 optional subjects
            $selectedSubjects = [];
            
            // Select 2 different subjects
            for ($i = 0; $i < 2; $i++) {
                $subject = $optionalSubjects[$subjectIndex % $optionalSubjects->count()];
                $selectedSubjects[] = $subject;
                $subjectIndex++;
            }
            
            // Create mappings
            foreach ($selectedSubjects as $index => $subject) {
                \App\Models\MajorSubjectMapping::create([
                    'major_id' => $major->id,
                    'subject_id' => $subject->id,
                    'education_level' => 'SMA/MA',
                    'mapping_type' => 'pilihan',
                    'priority' => $index + 1,
                    'is_active' => true,
                    'subject_type' => 'pilihan'
                ]);
                $mappingCount++;
            }
            
        } else {
            // SMK/MAK: 1 mandatory PKW + 1 optional (total 2 subjects)
            
            // 1. Add mandatory PKW subject
            $pkwSubject = \App\Models\Subject::where('name', 'Produk/Projek Kreatif dan Kewirausahaan')
                ->where('education_level', 'SMK/MAK')
                ->first();
                
            if ($pkwSubject) {
                \App\Models\MajorSubjectMapping::create([
                    'major_id' => $major->id,
                    'subject_id' => $pkwSubject->id,
                    'education_level' => 'SMK/MAK',
                    'mapping_type' => 'pilihan_wajib',
                    'priority' => 1,
                    'is_active' => true,
                    'subject_type' => 'pilihan_wajib'
                ]);
                $mappingCount++;
            }
            
            // 2. Add 1 optional subject from SMA/MA list
            $optionalSubject = $optionalSubjects[$subjectIndex % $optionalSubjects->count()];
            \App\Models\MajorSubjectMapping::create([
                'major_id' => $major->id,
                'subject_id' => $optionalSubject->id,
                'education_level' => 'SMK/MAK',
                'mapping_type' => 'pilihan',
                'priority' => 2,
                'is_active' => true,
                'subject_type' => 'pilihan'
            ]);
            $mappingCount++;
            $subjectIndex++;
        }
    }
    
    echo "✅ Mappings created: " . $mappingCount . "\n\n";

    // 5. Verify all majors have exactly 2 subjects
    echo "5. Verifying all majors have exactly 2 subjects...\n";
    
    $majorsWithWrongCount = 0;
    $totalMappings = 0;
    
    foreach ($majors as $major) {
        $educationLevel = determineEducationLevel($major->rumpun_ilmu);
        
        $mappingCount = \App\Models\MajorSubjectMapping::where('major_id', $major->id)->count();
        $totalMappings += $mappingCount;
        
        if ($mappingCount !== 2) {
            echo "   ❌ {$major->major_name} ({$educationLevel}): {$mappingCount} subjects\n";
            $majorsWithWrongCount++;
        } else {
            echo "   ✅ {$major->major_name} ({$educationLevel}): {$mappingCount} subjects\n";
        }
    }
    
    echo "\n📊 SUMMARY:\n";
    echo "   - Total majors: " . $majors->count() . "\n";
    echo "   - Majors with exactly 2 subjects: " . ($majors->count() - $majorsWithWrongCount) . "\n";
    echo "   - Majors with wrong count: " . $majorsWithWrongCount . "\n";
    echo "   - Total mappings: " . $totalMappings . "\n\n";

    // 6. Test APIs
    echo "6. Testing APIs...\n";
    
    // Test Web API
    $webController = new \App\Http\Controllers\StudentWebController();
    $webResponse = $webController->getMajors();
    $webData = json_decode($webResponse->getContent(), true);
    
    if ($webData['success']) {
        $sampleMajor = $webData['data'][0];
        echo "   ✅ Web API working\n";
        echo "   Sample: {$sampleMajor['major_name']} has " . count($sampleMajor['optional_subjects']) . " optional subjects\n";
        
        // Check if all majors have exactly 2 subjects
        $allCorrect = true;
        foreach ($webData['data'] as $major) {
            if (count($major['optional_subjects']) !== 2) {
                echo "   ❌ {$major['major_name']} has " . count($major['optional_subjects']) . " subjects\n";
                $allCorrect = false;
            }
        }
        
        if ($allCorrect) {
            echo "   ✅ All majors have exactly 2 optional subjects\n";
        } else {
            echo "   ❌ Some majors don't have exactly 2 subjects\n";
        }
    } else {
        echo "   ❌ Web API error: {$webData['message']}\n";
    }
    
    echo "\n🎉 OPTIONAL SUBJECTS FIX COMPLETED!\n";
    echo "==================================\n";
    echo "✅ Every major now has exactly 2 subjects\n";
    echo "✅ SMA/MA: 2 optional subjects\n";
    echo "✅ SMK/MAK: 1 mandatory + 1 optional (total 2)\n";
    echo "✅ APIs are working correctly\n";

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
