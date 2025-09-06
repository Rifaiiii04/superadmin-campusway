<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing PPKK subject for SMK/MAK...\n";

// Check if PPKK subject exists
$ppkk = DB::table('subjects')
    ->where('name', 'Produk/Projek Kreatif dan Kewirausahaan')
    ->first();

if ($ppkk) {
    echo "✅ PPKK subject found (ID: {$ppkk->id})\n";
    
    // Update to SMK education level and Pilihan Pertama type
    DB::table('subjects')
        ->where('id', $ppkk->id)
        ->update([
            'education_level' => 'SMK',
            'subject_type' => 'Pilihan Pertama',
            'subject_number' => 19,
            'updated_at' => now()
        ]);
    
    echo "✅ Updated PPKK to SMK Pilihan Pertama\n";
} else {
    echo "❌ PPKK subject not found!\n";
}

// Verify the fix
$updatedPpkk = DB::table('subjects')
    ->where('name', 'Produk/Projek Kreatif dan Kewirausahaan')
    ->first();

if ($updatedPpkk) {
    echo "\n📋 Updated PPKK details:\n";
    echo "  - Name: {$updatedPpkk->name}\n";
    echo "  - Education Level: {$updatedPpkk->education_level}\n";
    echo "  - Subject Type: {$updatedPpkk->subject_type}\n";
    echo "  - Subject Number: {$updatedPpkk->subject_number}\n";
}

echo "\n✅ PPKK fix completed!\n";
