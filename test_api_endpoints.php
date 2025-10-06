<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING API ENDPOINTS CONSISTENCY ===\n\n";

try {
    // Test 1: SuperAdmin API (internal)
    echo "1. SuperAdmin Internal API:\n";
    $majors = \App\Models\MajorRecommendation::with(['majorSubjectMappings.subject'])
        ->where('is_active', true)
        ->limit(3)
        ->get();
    
    foreach ($majors as $major) {
        $educationLevel = $major->rumpun_ilmu === 'ILMU TERAPAN' ? 'SMK/MAK' : 'SMA/MA';
        
        // Get optional subjects using same logic as SuperAdmin
        if ($educationLevel === 'SMK/MAK') {
            $optionalSubjects = \App\Helpers\SMKSubjectHelper::getSubjectsForMajor($major->major_name);
        } else {
            $optionalSubjects = $major->majorSubjectMappings
                ->filter(function($mapping) {
                    return $mapping->subject && 
                           $mapping->mapping_type === 'pilihan';
                })
                ->pluck('subject.name')
                ->toArray();
        }
        
        echo "   {$major->major_name}:\n";
        echo "     - Optional subjects: " . implode(', ', $optionalSubjects) . "\n";
        echo "     - Curriculum Merdeka: " . (is_array($major->kurikulum_merdeka_subjects) ? count($major->kurikulum_merdeka_subjects) : 0) . " subjects\n";
    }
    echo "\n";

    // Test 2: Web API (used by teacher/student)
    echo "2. Web API (Teacher/Student):\n";
    $webController = new \App\Http\Controllers\StudentWebController();
    $webResponse = $webController->getMajors();
    $webData = json_decode($webResponse->getContent(), true);
    
    if ($webData['success']) {
        foreach (array_slice($webData['data'], 0, 3) as $major) {
            echo "   {$major['major_name']}:\n";
            echo "     - Optional subjects: " . implode(', ', $major['optional_subjects']) . "\n";
            echo "     - Curriculum Merdeka: " . (isset($major['kurikulum_merdeka_subjects']) ? count($major['kurikulum_merdeka_subjects']) : 0) . " subjects\n";
        }
    } else {
        echo "   ❌ Web API Error: " . $webData['message'] . "\n";
    }
    echo "\n";

    // Test 3: Student Subject API
    echo "3. Student Subject API:\n";
    $student = \App\Models\Student::with('studentChoice.major')->first();
    if ($student && $student->studentChoice) {
        $studentController = new \App\Http\Controllers\StudentSubjectController();
        $request = new \Illuminate\Http\Request(['student_id' => $student->id]);
        $response = $studentController->getStudentSubjects($request);
        $studentData = json_decode($response->getContent(), true);
        
        if ($studentData['success']) {
            echo "   Student: {$student->name}\n";
            echo "   Major: {$studentData['data']['major']['major_name']}\n";
            echo "     - Optional subjects: " . implode(', ', array_column($studentData['data']['subjects']['optional'], 'name')) . "\n";
            echo "     - Curriculum Merdeka: " . count($studentData['data']['curriculum']['merdeka']) . " subjects\n";
        } else {
            echo "   ❌ Student API Error: " . $studentData['message'] . "\n";
        }
    } else {
        echo "   ❌ No student with choices found\n";
    }
    echo "\n";

    // Test 4: Check if Web API uses same logic as SuperAdmin
    echo "4. Consistency Check:\n";
    $firstMajor = $majors->first();
    $firstWebMajor = $webData['data'][0] ?? null;
    
    if ($firstWebMajor && $firstMajor->major_name === $firstWebMajor['major_name']) {
        echo "   Comparing: {$firstMajor->major_name}\n";
        
        // Get SuperAdmin optional subjects
        $educationLevel = $firstMajor->rumpun_ilmu === 'ILMU TERAPAN' ? 'SMK/MAK' : 'SMA/MA';
        if ($educationLevel === 'SMK/MAK') {
            $superAdminOptional = \App\Helpers\SMKSubjectHelper::getSubjectsForMajor($firstMajor->major_name);
        } else {
            $superAdminOptional = $firstMajor->majorSubjectMappings
                ->filter(function($mapping) {
                    return $mapping->subject && 
                           $mapping->mapping_type === 'pilihan';
                })
                ->pluck('subject.name')
                ->toArray();
        }
        
        $webOptional = $firstWebMajor['optional_subjects'];
        
        echo "   - SuperAdmin optional: " . implode(', ', $superAdminOptional) . "\n";
        echo "   - Web API optional: " . implode(', ', $webOptional) . "\n";
        
        $optionalMatch = (count(array_diff($superAdminOptional, $webOptional)) === 0 && 
                         count(array_diff($webOptional, $superAdminOptional)) === 0);
        echo "   - Optional subjects match: " . ($optionalMatch ? '✅ Yes' : '❌ No') . "\n";
        
        if (!$optionalMatch) {
            echo "   ⚠️  Web API needs to be updated to use same logic as SuperAdmin\n";
        }
    }

    echo "\n✅ API endpoints test completed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

?>
