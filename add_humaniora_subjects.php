<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🎨 Adding HUMANIORA subjects according to Pusmendik reference...\n";

// Subjects needed for HUMANIORA according to Pusmendik
$humanioraSubjects = [
    [
        'name' => 'Seni Budaya',
        'code' => 'SB',
        'description' => 'Mata pelajaran seni dan budaya untuk rumpun HUMANIORA',
        'is_required' => true,
        'is_active' => true,
        'education_level' => 'SMA',
        'subject_type' => 'Pendukung',
        'subject_number' => 20,
        'type' => 'pilihan'
    ],
    [
        'name' => 'Sejarah Indonesia',
        'code' => 'SI',
        'description' => 'Mata pelajaran sejarah Indonesia untuk kurikulum 2013',
        'is_required' => true,
        'is_active' => true,
        'education_level' => 'SMA',
        'subject_type' => 'Pendukung',
        'subject_number' => 21,
        'type' => 'pilihan'
    ],
    [
        'name' => 'Bahasa Indonesia',
        'code' => 'BI',
        'description' => 'Mata pelajaran bahasa Indonesia untuk kurikulum 2013',
        'is_required' => true,
        'is_active' => true,
        'education_level' => 'SMA',
        'subject_type' => 'Pendukung',
        'subject_number' => 22,
        'type' => 'pilihan'
    ],
    [
        'name' => 'Bahasa Inggris',
        'code' => 'BE',
        'description' => 'Mata pelajaran bahasa Inggris untuk kurikulum 2013',
        'is_required' => true,
        'is_active' => true,
        'education_level' => 'SMA',
        'subject_type' => 'Pendukung',
        'subject_number' => 23,
        'type' => 'pilihan'
    ]
];

$addedCount = 0;

foreach ($humanioraSubjects as $subjectData) {
    // Check if subject already exists
    $existing = DB::table('subjects')
        ->where('name', $subjectData['name'])
        ->first();

    if (!$existing) {
        DB::table('subjects')->insert(array_merge($subjectData, [
            'created_at' => now(),
            'updated_at' => now()
        ]));
        
        echo "✅ Added: {$subjectData['name']}\n";
        $addedCount++;
    } else {
        echo "⚠️ Already exists: {$subjectData['name']}\n";
    }
}

echo "\n🎉 HUMANIORA subjects addition completed!\n";
echo "📊 Subjects added: {$addedCount}\n";
echo "📋 All subjects follow Pusmendik reference for SMA/MA/SMK/MAK\n";
