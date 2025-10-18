<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\MajorRecommendation;

echo "=== CEK KATEGORI DI DATABASE ===\n\n";

// Cek semua kategori yang ada
$categories = MajorRecommendation::select('category')
    ->distinct()
    ->orderBy('category')
    ->pluck('category');

echo "Kategori yang ada di database:\n";
foreach ($categories as $category) {
    echo "- '$category'\n";
}

echo "\n=== JUMLAH PER KATEGORI ===\n";
$categoryCounts = MajorRecommendation::select('category', \DB::raw('count(*) as count'))
    ->groupBy('category')
    ->orderBy('category')
    ->get();

foreach ($categoryCounts as $item) {
    echo "- {$item->category}: {$item->count} jurusan\n";
}

echo "\n=== CONTOH DATA PER KATEGORI ===\n";
foreach ($categories as $category) {
    $sample = MajorRecommendation::where('category', $category)->first();
    if ($sample) {
        echo "- {$category}: {$sample->major_name}\n";
    }
}
