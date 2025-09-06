<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking valid kurikulum_type values...\n";

$kurikulumTypes = DB::table('program_studi_subjects')->select('kurikulum_type')->distinct()->get();
foreach($kurikulumTypes as $type) {
    echo "  - " . $type->kurikulum_type . "\n";
}

echo "\n✅ Kurikulum type values check completed!\n";
