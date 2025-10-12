<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$school = \App\Models\School::first();
if ($school) {
    $token = base64_encode(json_encode(['school_id' => $school->id, 'timestamp' => time()]));
    echo "Valid token: " . $token . PHP_EOL;
    echo "School ID: " . $school->id . PHP_EOL;
    echo "School Name: " . $school->school_name . PHP_EOL;
} else {
    echo "No schools found" . PHP_EOL;
}
?>
