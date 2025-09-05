<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking HUMANIORA Subject Mapping...\n";

$programs = ['Seni', 'Sejarah', 'Linguistik', 'Sastra', 'Filsafat'];

foreach($programs as $programName) {
    echo "\n📚 {$programName}:\n";
    
    try {
        $mappings = DB::table('program_studi_subjects')
            ->join('program_studi', 'program_studi_subjects.program_studi_id', '=', 'program_studi.id')
            ->join('subjects', 'program_studi_subjects.subject_id', '=', 'subjects.id')
            ->where('program_studi.name', $programName)
            ->select('subjects.name', 'program_studi_subjects.kurikulum_type')
            ->get();
        
        $grouped = $mappings->groupBy('kurikulum_type');
        
        foreach(['merdeka', '2013_ipa', '2013_ips', '2013_bahasa'] as $type) {
            $subjects = $grouped->get($type, collect())->pluck('name')->unique();
            if($subjects->count() > 0) {
                echo "  - {$type}: " . $subjects->implode(', ') . "\n";
            }
        }
    } catch(Exception $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Mapping check completed!\n";
