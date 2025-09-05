<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing API Response for Student/Teacher Dashboard...\n";

try {
    // Test major recommendations API
    $majors = DB::table('major_recommendations')
        ->select('id', 'major_name', 'rumpun_ilmu', 'description')
        ->where('is_active', true)
        ->limit(5)
        ->get();
    
    echo "✅ Major Recommendations API Response:\n";
    foreach($majors as $major) {
        echo "  - ID: {$major->id}\n";
        echo "    Name: {$major->major_name}\n";
        echo "    Rumpun Ilmu: {$major->rumpun_ilmu}\n";
        echo "    Description: " . substr($major->description, 0, 50) . "...\n";
        echo "\n";
    }
    
    // Test student choice API
    $studentChoice = DB::table('student_choices')
        ->join('major_recommendations', 'student_choices.major_id', '=', 'major_recommendations.id')
        ->select('student_choices.id', 'student_choices.student_id', 'major_recommendations.major_name', 'major_recommendations.rumpun_ilmu')
        ->limit(3)
        ->get();
    
    echo "✅ Student Choice API Response:\n";
    foreach($studentChoice as $choice) {
        echo "  - Student ID: {$choice->student_id}\n";
        echo "    Major: {$choice->major_name}\n";
        echo "    Rumpun Ilmu: {$choice->rumpun_ilmu}\n";
        echo "\n";
    }
    
} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "✅ API Response test completed!\n";
