<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Database configuration
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'campusway_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    echo "Starting rumpun_ilmu field fix...\n";
    
    // Add rumpun_ilmu column to major_recommendations table if it doesn't exist
    if (!Capsule::schema()->hasColumn('major_recommendations', 'rumpun_ilmu')) {
        echo "Adding rumpun_ilmu column to major_recommendations table...\n";
        Capsule::schema()->table('major_recommendations', function ($table) {
            $table->string('rumpun_ilmu')->nullable()->after('category');
        });
        echo "✅ Column added successfully!\n";
    } else {
        echo "✅ Column rumpun_ilmu already exists!\n";
    }
    
    // Update rumpun_ilmu based on category
    echo "Updating rumpun_ilmu values based on category...\n";
    
    $mapping = [
        'Saintek' => 'ILMU ALAM',
        'Soshum' => 'ILMU SOSIAL',
        'Humaniora' => 'HUMANIORA',
        'Ilmu Terapan' => 'ILMU TERAPAN',
        'Ilmu Formal' => 'ILMU FORMAL',
        'Ilmu Alam' => 'ILMU ALAM',
        'Ilmu Sosial' => 'ILMU SOSIAL',
    ];
    
    $updated = 0;
    foreach ($mapping as $category => $rumpunIlmu) {
        $count = Capsule::table('major_recommendations')
            ->where('category', $category)
            ->whereNull('rumpun_ilmu')
            ->update(['rumpun_ilmu' => $rumpunIlmu]);
        
        if ($count > 0) {
            echo "✅ Updated {$count} records with category '{$category}' to rumpun_ilmu '{$rumpunIlmu}'\n";
            $updated += $count;
        }
    }
    
    // Handle special cases for specific majors
    $specialCases = [
        'Administrasi Bisnis' => 'ILMU TERAPAN',
        'Arsitektur' => 'ILMU TERAPAN',
        'Teknik Informatika' => 'ILMU TERAPAN',
        'Teknik Mesin' => 'ILMU TERAPAN',
        'Teknik Elektro' => 'ILMU TERAPAN',
        'Akuntansi' => 'ILMU SOSIAL',
        'Manajemen' => 'ILMU SOSIAL',
        'Ekonomi' => 'ILMU SOSIAL',
        'Psikologi' => 'ILMU SOSIAL',
        'Hukum' => 'ILMU SOSIAL',
        'Pendidikan' => 'ILMU SOSIAL',
        'Komunikasi' => 'ILMU SOSIAL',
        'Seni' => 'HUMANIORA',
        'Sejarah' => 'HUMANIORA',
        'Linguistik' => 'HUMANIORA',
        'Sastra' => 'HUMANIORA',
        'Matematika' => 'ILMU FORMAL',
        'Komputer' => 'ILMU FORMAL',
        'Logika' => 'ILMU FORMAL',
        'Fisika' => 'ILMU ALAM',
        'Kimia' => 'ILMU ALAM',
        'Biologi' => 'ILMU ALAM',
        'Astronomi' => 'ILMU ALAM',
    ];
    
    foreach ($specialCases as $majorName => $rumpunIlmu) {
        $count = Capsule::table('major_recommendations')
            ->where('major_name', 'LIKE', "%{$majorName}%")
            ->update(['rumpun_ilmu' => $rumpunIlmu]);
        
        if ($count > 0) {
            echo "✅ Updated {$count} records for major '{$majorName}' to rumpun_ilmu '{$rumpunIlmu}'\n";
            $updated += $count;
        }
    }
    
    echo "\n🎉 Fix completed! Total records updated: {$updated}\n";
    
    // Show some examples
    echo "\n📋 Sample data after update:\n";
    $samples = Capsule::table('major_recommendations')
        ->select('major_name', 'category', 'rumpun_ilmu')
        ->whereNotNull('rumpun_ilmu')
        ->limit(10)
        ->get();
    
    foreach ($samples as $sample) {
        echo "- {$sample->major_name}: {$sample->category} → {$sample->rumpun_ilmu}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
