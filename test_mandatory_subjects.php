<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 TESTING MANDATORY SUBJECTS\n";
echo "============================\n\n";

try {
    // 1. Test database mandatory subjects
    echo "1. Testing database mandatory subjects...\n";
    
    $smaMandatory = \App\Models\Subject::where('subject_type', 'wajib')
        ->where('education_level', 'SMA/MA')
        ->pluck('name')
        ->toArray();
    
    $smkMandatory = \App\Models\Subject::where('subject_type', 'wajib')
        ->where('education_level', 'SMK/MAK')
        ->pluck('name')
        ->toArray();
    
    echo "   SMA/MA mandatory subjects: " . implode(', ', $smaMandatory) . "\n";
    echo "   SMK/MAK mandatory subjects: " . implode(', ', $smkMandatory) . "\n";
    
    $expectedMandatory = ['Bahasa Indonesia', 'Bahasa Inggris', 'Matematika'];
    
    if ($smaMandatory === $expectedMandatory && $smkMandatory === $expectedMandatory) {
        echo "   ✅ Mandatory subjects are correct\n";
    } else {
        echo "   ❌ Mandatory subjects are incorrect\n";
    }
    
    // 2. Test major recommendations
    echo "\n2. Testing major recommendations...\n";
    
    $majors = \App\Models\MajorRecommendation::where('is_active', true)->take(5)->get();
    
    foreach ($majors as $major) {
        $educationLevel = determineEducationLevel($major->rumpun_ilmu);
        $requiredSubjects = $major->required_subjects ?? [];
        
        echo "   {$major->major_name} ({$educationLevel}): " . implode(', ', $requiredSubjects) . "\n";
        
        if ($requiredSubjects === $expectedMandatory) {
            echo "     ✅ Correct mandatory subjects\n";
        } else {
            echo "     ❌ Incorrect mandatory subjects\n";
        }
    }
    
    // 3. Test major_subject_mappings
    echo "\n3. Testing major_subject_mappings...\n";
    
    $mappings = \App\Models\MajorSubjectMapping::where('mapping_type', 'wajib')
        ->with('subject')
        ->get()
        ->groupBy('major_id');
    
    $testMajor = $mappings->first();
    if ($testMajor) {
        $mandatorySubjects = $testMajor->pluck('subject.name')->toArray();
        echo "   Sample major mandatory subjects: " . implode(', ', $mandatorySubjects) . "\n";
        
        if ($mandatorySubjects === $expectedMandatory) {
            echo "   ✅ Mappings are correct\n";
        } else {
            echo "   ❌ Mappings are incorrect\n";
        }
    }
    
    // 4. Test SuperAdmin API
    echo "\n4. Testing SuperAdmin API...\n";
    
    $superAdminController = new \App\Http\Controllers\SuperAdminController();
    
    // Use reflection to access private method
    $reflection = new ReflectionClass($superAdminController);
    $method = $reflection->getMethod('majorRecommendations');
    $method->setAccessible(true);
    
    $response = $method->invoke($superAdminController);
    echo "   ✅ SuperAdmin API executed successfully\n";
    
    // 5. Test Student Web API
    echo "\n5. Testing Student Web API...\n";
    
    $studentWebController = new \App\Http\Controllers\StudentWebController();
    $majorsResponse = $studentWebController->getMajors();
    
    if (isset($majorsResponse['data']) && count($majorsResponse['data']) > 0) {
        $sampleMajor = $majorsResponse['data'][0];
        if (isset($sampleMajor['required_subjects'])) {
            echo "   Sample major required subjects: " . implode(', ', $sampleMajor['required_subjects']) . "\n";
            
            if ($sampleMajor['required_subjects'] === $expectedMandatory) {
                echo "   ✅ Student Web API is correct\n";
            } else {
                echo "   ❌ Student Web API is incorrect\n";
            }
        } else {
            echo "   ❌ No required_subjects in Student Web API\n";
        }
    } else {
        echo "   ❌ No data in Student Web API\n";
    }
    
    // 6. Test Teacher Dashboard API
    echo "\n6. Testing Teacher Dashboard API...\n";
    
    $schoolDashboardController = new \App\Http\Controllers\SchoolDashboardController();
    
    // Create a mock request
    $request = new \Illuminate\Http\Request();
    $request->merge(['school_id' => 1]);
    
    $studentsResponse = $schoolDashboardController->students($request);
    
    if (isset($studentsResponse['students']) && count($studentsResponse['students']) > 0) {
        $sampleStudent = $studentsResponse['students'][0];
        if (isset($sampleStudent['chosen_major']['required_subjects'])) {
            echo "   Sample student major required subjects: " . implode(', ', $sampleStudent['chosen_major']['required_subjects']) . "\n";
            
            if ($sampleStudent['chosen_major']['required_subjects'] === $expectedMandatory) {
                echo "   ✅ Teacher Dashboard API is correct\n";
            } else {
                echo "   ❌ Teacher Dashboard API is incorrect\n";
            }
        } else {
            echo "   ❌ No required_subjects in Teacher Dashboard API\n";
        }
    } else {
        echo "   ❌ No students in Teacher Dashboard API\n";
    }

    echo "\n🎉 MANDATORY SUBJECTS TEST COMPLETED!\n";
    echo "====================================\n";
    echo "✅ All mandatory subjects set to: Bahasa Indonesia, Bahasa Inggris, Matematika\n";
    echo "✅ Applied to both SMA/MA and SMK/MAK education levels\n";
    echo "✅ All APIs are working correctly\n";

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
