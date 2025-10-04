<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING API CONSISTENCY ===\n\n";

try {
    // Test 1: Get majors from StudentWebController
    echo "1. Testing StudentWebController::getMajors()\n";
    $studentController = new \App\Http\Controllers\StudentWebController();
    $majorsResponse = $studentController->getMajors();
    $majorsData = json_decode($majorsResponse->getContent(), true);
    
    echo "   Total majors: " . count($majorsData['data']) . "\n";
    echo "   Sample majors:\n";
    foreach (array_slice($majorsData['data'], 0, 3) as $major) {
        echo "   - {$major['major_name']} ({$major['rumpun_ilmu']})\n";
    }
    echo "\n";

    // Test 2: Get major details with subjects
    echo "2. Testing StudentWebController::getMajorDetails()\n";
    $firstMajor = $majorsData['data'][0];
    $majorDetailsResponse = $studentController->getMajorDetails($firstMajor['id']);
    $majorDetailsData = json_decode($majorDetailsResponse->getContent(), true);
    
    if ($majorDetailsData['success']) {
        echo "   Major: {$majorDetailsData['data']['major_name']}\n";
        echo "   Required subjects: " . implode(', ', $majorDetailsData['data']['subjects']['required']) . "\n";
        echo "   Preferred subjects: " . implode(', ', $majorDetailsData['data']['subjects']['preferred']) . "\n";
    }
    echo "\n";

    // Test 3: Get subjects for major using StudentSubjectController
    echo "3. Testing StudentSubjectController::getSubjectsForMajor()\n";
    $subjectController = new \App\Http\Controllers\StudentSubjectController();
    
    // Test SMA/MA
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'major_id' => $firstMajor['id'],
        'education_level' => 'SMA/MA'
    ]);
    
    $subjectsResponse = $subjectController->getSubjectsForMajor($request);
    $subjectsData = json_decode($subjectsResponse->getContent(), true);
    
    if ($subjectsData['success']) {
        echo "   Education Level: {$subjectsData['data']['education_level']}\n";
        echo "   Mandatory subjects: " . count($subjectsData['data']['subjects']['mandatory']) . "\n";
        echo "   Optional subjects: " . count($subjectsData['data']['subjects']['optional']) . "\n";
        echo "   Optional subject names: ";
        foreach ($subjectsData['data']['subjects']['optional'] as $subject) {
            echo $subject['name'] . " ";
        }
        echo "\n";
    }
    echo "\n";

    // Test 4: Compare with SuperAdmin data
    echo "4. Testing SuperAdminController::majorRecommendations()\n";
    $superAdminController = new \App\Http\Controllers\SuperAdminController();
    $superAdminResponse = $superAdminController->majorRecommendations();
    
    // Get the data from the response (it's an Inertia response)
    $superAdminData = $superAdminResponse->getData();
    
    if (isset($superAdminData['majorRecommendations'])) {
        $superAdminMajors = $superAdminData['majorRecommendations'];
        echo "   Total majors in SuperAdmin: " . count($superAdminMajors) . "\n";
        
        // Find the same major
        $sameMajor = collect($superAdminMajors)->firstWhere('id', $firstMajor['id']);
        if ($sameMajor) {
            echo "   Same major found: {$sameMajor['major_name']}\n";
            echo "   Optional subjects: " . implode(', ', $sameMajor['optional_subjects']) . "\n";
        }
    }
    echo "\n";

    // Test 5: Check database mapping directly
    echo "5. Testing database mapping directly\n";
    $mappings = DB::table('major_subject_mappings')
        ->join('major_recommendations', 'major_subject_mappings.major_id', '=', 'major_recommendations.id')
        ->join('subjects', 'major_subject_mappings.subject_id', '=', 'subjects.id')
        ->where('major_recommendations.id', $firstMajor['id'])
        ->where('major_subject_mappings.education_level', 'SMA/MA')
        ->where('major_subject_mappings.mapping_type', 'pilihan')
        ->select('subjects.name', 'major_subject_mappings.priority')
        ->orderBy('major_subject_mappings.priority')
        ->get();
    
    echo "   Database mappings for major {$firstMajor['id']}:\n";
    foreach ($mappings as $mapping) {
        echo "   - {$mapping->name} (priority: {$mapping->priority})\n";
    }

    echo "\n✅ API consistency test completed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

?>
