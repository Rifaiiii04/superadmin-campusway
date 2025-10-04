<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING ALL DASHBOARDS CONSISTENCY ===\n\n";

try {
    // Test 1: SuperAdmin Dashboard Data
    echo "1. SuperAdmin Dashboard Data:\n";
    $superAdminController = new \App\Http\Controllers\SuperAdminController();
    $superAdminData = $superAdminController->majorRecommendations();
    $superAdminMajors = $superAdminData->getData()['props']['majorRecommendations'];
    
    echo "   Total majors: " . count($superAdminMajors) . "\n";
    echo "   Sample major: {$superAdminMajors[0]['major_name']}\n";
    echo "   - Optional subjects: " . count($superAdminMajors[0]['optional_subjects']) . "\n";
    echo "   - Has curriculum data: " . (isset($superAdminMajors[0]['kurikulum_merdeka_subjects']) ? 'Yes' : 'No') . "\n";
    echo "\n";

    // Test 2: Student API Data
    echo "2. Student API Data:\n";
    $student = \App\Models\Student::with('studentChoices.major')->first();
    if ($student && $student->studentChoices->count() > 0) {
        $studentChoice = $student->studentChoices->first();
        $studentController = new \App\Http\Controllers\StudentSubjectController();
        $request = new \Illuminate\Http\Request(['student_id' => $student->id]);
        $response = $studentController->getStudentSubjects($request);
        $studentData = json_decode($response->getContent(), true);
        
        if ($studentData['success']) {
            echo "   Student: {$student->name}\n";
            echo "   Major: {$studentData['data']['major']['major_name']}\n";
            echo "   - Optional subjects: " . count($studentData['data']['subjects']['optional']) . "\n";
            echo "   - Has curriculum data: " . (isset($studentData['data']['curriculum']) ? 'Yes' : 'No') . "\n";
            if (isset($studentData['data']['curriculum'])) {
                echo "   - Merdeka: " . count($studentData['data']['curriculum']['merdeka']) . " subjects\n";
                echo "   - 2013 IPA: " . count($studentData['data']['curriculum']['2013_ipa']) . " subjects\n";
            }
        } else {
            echo "   ❌ Student API Error: " . $studentData['message'] . "\n";
        }
    } else {
        echo "   ❌ No student with choices found\n";
    }
    echo "\n";

    // Test 3: Teacher API Data (Web API)
    echo "3. Teacher API Data (Web API):\n";
    $webController = new \App\Http\Controllers\StudentWebController();
    $webResponse = $webController->getMajors();
    $webData = json_decode($webResponse->getContent(), true);
    
    if ($webData['success']) {
        echo "   Total majors: " . count($webData['data']) . "\n";
        echo "   Sample major: {$webData['data'][0]['major_name']}\n";
        echo "   - Optional subjects: " . count($webData['data'][0]['optional_subjects']) . "\n";
        echo "   - Has curriculum data: " . (isset($webData['data'][0]['kurikulum_merdeka_subjects']) ? 'Yes' : 'No') . "\n";
    } else {
        echo "   ❌ Web API Error: " . $webData['message'] . "\n";
    }
    echo "\n";

    // Test 4: Compare data consistency
    echo "4. Data Consistency Check:\n";
    
    // Get a specific major from each source
    $superAdminMajor = $superAdminMajors[0];
    $webMajor = $webData['data'][0];
    
    echo "   Comparing: {$superAdminMajor['major_name']} vs {$webMajor['major_name']}\n";
    
    // Check if they're the same major
    if ($superAdminMajor['major_name'] === $webMajor['major_name']) {
        echo "   ✅ Same major being compared\n";
        
        // Compare optional subjects
        $superAdminOptional = $superAdminMajor['optional_subjects'];
        $webOptional = $webMajor['optional_subjects'];
        
        echo "   - SuperAdmin optional: " . implode(', ', $superAdminOptional) . "\n";
        echo "   - Web API optional: " . implode(', ', $webOptional) . "\n";
        
        $optionalMatch = (count(array_diff($superAdminOptional, $webOptional)) === 0 && 
                         count(array_diff($webOptional, $superAdminOptional)) === 0);
        echo "   - Optional subjects match: " . ($optionalMatch ? '✅ Yes' : '❌ No') . "\n";
        
        // Compare curriculum data
        $superAdminCurriculum = isset($superAdminMajor['kurikulum_merdeka_subjects']) ? $superAdminMajor['kurikulum_merdeka_subjects'] : [];
        $webCurriculum = isset($webMajor['kurikulum_merdeka_subjects']) ? $webMajor['kurikulum_merdeka_subjects'] : [];
        
        echo "   - SuperAdmin curriculum: " . count($superAdminCurriculum) . " subjects\n";
        echo "   - Web API curriculum: " . count($webCurriculum) . " subjects\n";
        
        $curriculumMatch = (count(array_diff($superAdminCurriculum, $webCurriculum)) === 0 && 
                           count(array_diff($webCurriculum, $superAdminCurriculum)) === 0);
        echo "   - Curriculum data match: " . ($curriculumMatch ? '✅ Yes' : '❌ No') . "\n";
        
    } else {
        echo "   ⚠️  Different majors being compared\n";
    }

    echo "\n✅ All dashboards consistency test completed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

?>
