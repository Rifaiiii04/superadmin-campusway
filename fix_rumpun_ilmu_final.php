<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "🔧 Final fix for rumpun ilmu categories...\n";
    
    // Create SQLite database if not exists
    $dbPath = __DIR__ . '/database/database.sqlite';
    if (!file_exists($dbPath)) {
        touch($dbPath);
        echo "✅ Created SQLite database\n";
    }
    
    // Run migrations
    echo "📊 Running migrations...\n";
    Artisan::call('migrate', ['--force' => true]);
    
    // Check if major_recommendations table exists and has data
    $count = DB::table('major_recommendations')->count();
    echo "📋 Current major recommendations count: $count\n";
    
    if ($count == 0) {
        echo "📝 Creating sample data...\n";
        
        // Insert sample data with correct categories
        $sampleData = [
            [
                'major_name' => 'Seni',
                'description' => 'Program studi seni dan budaya',
                'category' => 'Humaniora',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'major_name' => 'Sosial',
                'description' => 'Program studi ilmu sosial',
                'category' => 'Ilmu Sosial',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'major_name' => 'Kimia',
                'description' => 'Program studi ilmu alam',
                'category' => 'Ilmu Alam',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'major_name' => 'Komputer',
                'description' => 'Program studi ilmu formal',
                'category' => 'Ilmu Formal',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'major_name' => 'Pertanian',
                'description' => 'Program studi ilmu terapan',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'major_name' => 'Matematika',
                'description' => 'Program studi matematika',
                'category' => 'Ilmu Formal',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'major_name' => 'Fisika',
                'description' => 'Program studi fisika',
                'category' => 'Ilmu Formal',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'major_name' => 'Ekonomi',
                'description' => 'Program studi ekonomi',
                'category' => 'Ilmu Sosial',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'major_name' => 'Teknik',
                'description' => 'Program studi teknik',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'major_name' => 'Sejarah',
                'description' => 'Program studi sejarah',
                'category' => 'Humaniora',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        
        foreach ($sampleData as $data) {
            DB::table('major_recommendations')->insert($data);
        }
        
        echo "✅ Sample data created\n";
    } else {
        echo "📝 Updating existing data...\n";
        
        // Update existing data to have correct categories
        $updates = [
            'Seni' => 'Humaniora',
            'Sosial' => 'Ilmu Sosial', 
            'Kimia' => 'Ilmu Alam',
            'Komputer' => 'Ilmu Formal',
            'Pertanian' => 'Ilmu Terapan',
            'Matematika' => 'Ilmu Formal',
            'Fisika' => 'Ilmu Formal',
            'Ekonomi' => 'Ilmu Sosial',
            'Teknik' => 'Ilmu Terapan',
            'Sejarah' => 'Humaniora'
        ];
        
        foreach ($updates as $majorName => $category) {
            DB::table('major_recommendations')
                ->where('major_name', $majorName)
                ->update(['category' => $category]);
        }
        
        echo "✅ Data updated\n";
    }
    
    // Show final categories
    echo "\n📊 Final categories in database:\n";
    $categories = DB::table('major_recommendations')
        ->select('category', DB::raw('count(*) as count'))
        ->groupBy('category')
        ->orderBy('category')
        ->get();
        
    foreach ($categories as $cat) {
        echo "- {$cat->category}: {$cat->count} majors\n";
    }
    
    echo "\n✅ Rumpun ilmu fix completed!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
