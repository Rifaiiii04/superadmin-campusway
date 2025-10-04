<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING CURRICULUM DATA ===\n\n";

try {
    // Test 1: Check if majors have curriculum data
    echo "1. Checking curriculum data in major_recommendations:\n";
    $majors = \App\Models\MajorRecommendation::where('is_active', true)
        ->limit(5)
        ->get();
    
    foreach ($majors as $major) {
        echo "   {$major->major_name}:\n";
        echo "     - Kurikulum Merdeka: " . (is_array($major->kurikulum_merdeka_subjects) ? count($major->kurikulum_merdeka_subjects) : 0) . " subjects\n";
        echo "     - Kurikulum 2013 IPA: " . (is_array($major->kurikulum_2013_ipa_subjects) ? count($major->kurikulum_2013_ipa_subjects) : 0) . " subjects\n";
        echo "     - Kurikulum 2013 IPS: " . (is_array($major->kurikulum_2013_ips_subjects) ? count($major->kurikulum_2013_ips_subjects) : 0) . " subjects\n";
        echo "     - Kurikulum 2013 Bahasa: " . (is_array($major->kurikulum_2013_bahasa_subjects) ? count($major->kurikulum_2013_bahasa_subjects) : 0) . " subjects\n";
        echo "     - Career Prospects: " . (empty($major->career_prospects) ? 'Empty' : 'Has data') . "\n";
        echo "\n";
    }

    // Test 2: Check API response
    echo "2. Testing API response for student subjects:\n";
    $student = \App\Models\Student::first();
    if ($student) {
        echo "   Testing with student: {$student->name} (ID: {$student->id})\n";
        
        // Simulate API call
        $controller = new \App\Http\Controllers\StudentSubjectController();
        $request = new \Illuminate\Http\Request(['student_id' => $student->id]);
        $response = $controller->getStudentSubjects($request);
        $data = json_decode($response->getContent(), true);
        
        if ($data['success']) {
            echo "   ✅ API Success\n";
            echo "   - Has curriculum data: " . (isset($data['data']['curriculum']) ? 'Yes' : 'No') . "\n";
            if (isset($data['data']['curriculum'])) {
                echo "   - Merdeka: " . count($data['data']['curriculum']['merdeka']) . " subjects\n";
                echo "   - 2013 IPA: " . count($data['data']['curriculum']['2013_ipa']) . " subjects\n";
                echo "   - 2013 IPS: " . count($data['data']['curriculum']['2013_ips']) . " subjects\n";
                echo "   - 2013 Bahasa: " . count($data['data']['curriculum']['2013_bahasa']) . " subjects\n";
            }
            echo "   - Has career prospects: " . (!empty($data['data']['career_prospects']) ? 'Yes' : 'No') . "\n";
        } else {
            echo "   ❌ API Error: " . $data['message'] . "\n";
        }
    } else {
        echo "   ❌ No students found\n";
    }
    echo "\n";

    // Test 3: Check if we need to populate curriculum data
    echo "3. Checking if curriculum data needs to be populated:\n";
    $majorsWithoutCurriculum = \App\Models\MajorRecommendation::where('is_active', true)
        ->where(function($query) {
            $query->whereNull('kurikulum_merdeka_subjects')
                  ->orWhere('kurikulum_merdeka_subjects', '[]')
                  ->orWhere('kurikulum_merdeka_subjects', 'null');
        })
        ->count();
    
    echo "   Majors without curriculum data: {$majorsWithoutCurriculum}\n";
    
    if ($majorsWithoutCurriculum > 0) {
        echo "   ⚠️  Need to populate curriculum data\n";
    } else {
        echo "   ✅ All majors have curriculum data\n";
    }

    echo "\n✅ Curriculum data test completed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>
