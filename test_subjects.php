<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Subjects Data...\n\n";

// Test subjects
$subjects = DB::table('subjects')->orderBy('subject_number')->get();

echo "📚 Subjects List (19 mata pelajaran):\n";
echo "=====================================\n\n";

// Wajib
echo "📖 MATA PELAJARAN WAJIB (3):\n";
$wajib = $subjects->where('type', 'wajib');
foreach ($wajib as $subject) {
    echo "  {$subject->subject_number}. {$subject->name} ({$subject->code})\n";
}

echo "\n📚 MATA PELAJARAN PILIHAN (16):\n";
$pilihan = $subjects->where('type', 'pilihan');
foreach ($pilihan as $subject) {
    echo "  {$subject->subject_number}. {$subject->name} ({$subject->code})\n";
}

echo "\n📊 Summary:\n";
echo "  - Total: " . $subjects->count() . " subjects\n";
echo "  - Wajib: " . $wajib->count() . " subjects\n";
echo "  - Pilihan: " . $pilihan->count() . " subjects\n";

echo "\n✅ Subjects data is ready!\n";
