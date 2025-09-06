<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📚 Available subjects:\n";

$subjects = DB::table('subjects')->orderBy('name')->get();
foreach($subjects as $subject) {
    echo "  {$subject->id}: {$subject->name}\n";
}

echo "\n✅ Total subjects: " . $subjects->count() . "\n";
