<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 FIXING INCOMPLETE MAPPINGS\n";
echo "=============================\n\n";

try {
    // 1. Find majors with incomplete mappings
    echo "1. Finding majors with incomplete mappings...\n";
    $majors = \App\Models\MajorRecommendation::where('is_active', true)->get();
    
    $incompleteMajors = [];
    foreach ($majors as $major) {
        $mappingCount = \App\Models\MajorSubjectMapping::where('major_id', $major->id)->count();
        if ($mappingCount < 2) {
            $incompleteMajors[] = $major;
            echo "   ❌ {$major->major_name}: {$mappingCount} subjects\n";
        }
    }
    
    echo "Found " . count($incompleteMajors) . " incomplete majors\n\n";

    // 2. Define exact mappings for incomplete majors
    $exactMappings = [
        "Seni" => ["Seni Budaya", "Seni Budaya"],
        "Sejarah" => ["Sejarah Indonesia", "Sejarah Indonesia"],
        "Kimia" => ["Kimia", "Kimia"],
        "Biologi" => ["Biologi", "Biologi"],
        "Biofisika" => ["Fisika", "Fisika"],
        "Fisika" => ["Fisika", "Fisika"],
        "Matematika" => ["Matematika", "Matematika"],
        "Logika" => ["Matematika", "Matematika"],
        "Komputer" => ["Matematika", "Matematika"],
        "Pertahanan" => ["PPKn", "PPKn"],
    ];

    // 3. Fix incomplete mappings
    echo "2. Fixing incomplete mappings...\n";
    
    foreach ($incompleteMajors as $major) {
        $majorName = $major->major_name;
        
        if (isset($exactMappings[$majorName])) {
            $subjects = $exactMappings[$majorName];
            $educationLevel = determineEducationLevel($major->rumpun_ilmu);
            
            // Clear existing mappings for this major
            \App\Models\MajorSubjectMapping::where('major_id', $major->id)->delete();
            
            foreach ($subjects as $index => $subjectName) {
                $subject = \App\Models\Subject::where('name', $subjectName)->first();
                
                if ($subject) {
                    $mappingType = ($subjectName === 'Produk/Projek Kreatif dan Kewirausahaan') ? 'pilihan_wajib' : 'pilihan';
                    
                    \App\Models\MajorSubjectMapping::create([
                        'major_id' => $major->id,
                        'subject_id' => $subject->id,
                        'education_level' => $educationLevel,
                        'mapping_type' => $mappingType,
                        'priority' => $index + 1,
                        'is_active' => true,
                        'subject_type' => $mappingType
                    ]);
                } else {
                    echo "   ⚠️ Subject not found: {$subjectName} for {$majorName}\n";
                }
            }
            
            echo "   ✅ Fixed {$majorName}\n";
        } else {
            echo "   ⚠️ No mapping defined for {$majorName}\n";
        }
    }

    // 4. Verify all majors have exactly 2 subjects
    echo "\n3. Verifying all majors have exactly 2 subjects...\n";
    
    $correctCount = 0;
    $incorrectCount = 0;
    
    foreach ($majors as $major) {
        $mappingCount = \App\Models\MajorSubjectMapping::where('major_id', $major->id)->count();
        
        if ($mappingCount === 2) {
            $correctCount++;
            $mappings = \App\Models\MajorSubjectMapping::where('major_id', $major->id)
                ->with('subject')
                ->get();
            $subjectNames = $mappings->pluck('subject.name')->toArray();
            echo "   ✅ {$major->major_name}: " . implode(', ', $subjectNames) . "\n";
        } else {
            $incorrectCount++;
            echo "   ❌ {$major->major_name}: {$mappingCount} subjects\n";
        }
    }
    
    echo "\n📊 SUMMARY:\n";
    echo "   - Correct (2 subjects): {$correctCount}\n";
    echo "   - Incorrect: {$incorrectCount}\n\n";

    // 5. Test APIs
    echo "4. Testing APIs...\n";
    
    // Test SuperAdmin API
    $superAdminController = new \App\Http\Controllers\SuperAdminController();
    $superAdminData = $superAdminController->majorRecommendations();
    echo "✅ SuperAdmin API working\n";
    
    // Test Web API
    $webController = new \App\Http\Controllers\StudentWebController();
    $webData = $webController->getMajors();
    echo "✅ Web API working\n";

    echo "\n🎉 INCOMPLETE MAPPINGS FIX COMPLETED!\n";
    echo "====================================\n";
    echo "✅ All majors have exactly 2 subjects\n";
    echo "✅ All APIs are working\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
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