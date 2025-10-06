<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 CHECKING CURRICULUM DATA\n";
echo "==========================\n\n";

try {
    // 1. Check curriculum data in major_recommendations
    echo "1. Checking curriculum data in major_recommendations...\n";
    
    $majors = \App\Models\MajorRecommendation::where('is_active', true)
        ->take(5)
        ->get(['id', 'major_name', 'kurikulum_merdeka_subjects', 'kurikulum_2013_ipa_subjects', 'kurikulum_2013_ips_subjects', 'kurikulum_2013_bahasa_subjects']);
    
    foreach ($majors as $major) {
        echo "\n{$major->major_name}:\n";
        echo "  Merdeka: " . json_encode($major->kurikulum_merdeka_subjects) . "\n";
        echo "  2013 IPA: " . json_encode($major->kurikulum_2013_ipa_subjects) . "\n";
        echo "  2013 IPS: " . json_encode($major->kurikulum_2013_ips_subjects) . "\n";
        echo "  2013 Bahasa: " . json_encode($major->kurikulum_2013_bahasa_subjects) . "\n";
    }
    
    echo "\n2. Checking if curriculum data is empty...\n";
    
    $emptyMerdeka = \App\Models\MajorRecommendation::where('is_active', true)
        ->where(function($query) {
            $query->whereNull('kurikulum_merdeka_subjects')
                  ->orWhere('kurikulum_merdeka_subjects', '[]')
                  ->orWhere('kurikulum_merdeka_subjects', '');
        })
        ->count();
    
    $empty2013Ipa = \App\Models\MajorRecommendation::where('is_active', true)
        ->where(function($query) {
            $query->whereNull('kurikulum_2013_ipa_subjects')
                  ->orWhere('kurikulum_2013_ipa_subjects', '[]')
                  ->orWhere('kurikulum_2013_ipa_subjects', '');
        })
        ->count();
    
    echo "  Majors with empty Merdeka: {$emptyMerdeka}\n";
    echo "  Majors with empty 2013 IPA: {$empty2013Ipa}\n";
    
    echo "\n3. Sample curriculum data that should be populated...\n";
    
    // Sample curriculum data based on PUSMENDIK
    $sampleCurriculum = [
        'kurikulum_merdeka_subjects' => [
            'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Pendidikan Pancasila',
            'Pendidikan Agama', 'PJOK', 'Informatika', 'Sejarah'
        ],
        'kurikulum_2013_ipa_subjects' => [
            'Matematika', 'Fisika', 'Kimia', 'Biologi', 'Bahasa Indonesia',
            'Bahasa Inggris', 'Pendidikan Pancasila', 'Pendidikan Agama'
        ],
        'kurikulum_2013_ips_subjects' => [
            'Matematika', 'Ekonomi', 'Geografi', 'Sosiologi', 'Bahasa Indonesia',
            'Bahasa Inggris', 'Pendidikan Pancasila', 'Pendidikan Agama'
        ],
        'kurikulum_2013_bahasa_subjects' => [
            'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Sastra Indonesia',
            'Antropologi', 'Pendidikan Pancasila', 'Pendidikan Agama'
        ]
    ];
    
    echo "  Sample Merdeka: " . implode(', ', $sampleCurriculum['kurikulum_merdeka_subjects']) . "\n";
    echo "  Sample 2013 IPA: " . implode(', ', $sampleCurriculum['kurikulum_2013_ipa_subjects']) . "\n";
    echo "  Sample 2013 IPS: " . implode(', ', $sampleCurriculum['kurikulum_2013_ips_subjects']) . "\n";
    echo "  Sample 2013 Bahasa: " . implode(', ', $sampleCurriculum['kurikulum_2013_bahasa_subjects']) . "\n";

    echo "\n🎯 CONCLUSION:\n";
    if ($emptyMerdeka > 0) {
        echo "❌ Curriculum data is missing or empty\n";
        echo "✅ Need to populate curriculum data for all majors\n";
    } else {
        echo "✅ Curriculum data exists\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
