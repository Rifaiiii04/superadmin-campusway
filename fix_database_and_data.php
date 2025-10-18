<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "🔧 Fixing database and adding sample data...\n";
    
    // Test database connection
    echo "📊 Testing database connection...\n";
    DB::connection()->getPdo();
    echo "✅ Database connected successfully!\n";
    
    // Check if major_recommendations table exists
    $tableExists = DB::getSchemaBuilder()->hasTable('major_recommendations');
    echo "📋 Table exists: " . ($tableExists ? "Yes" : "No") . "\n";
    
    if (!$tableExists) {
        echo "📝 Creating major_recommendations table...\n";
        DB::statement("
            CREATE TABLE major_recommendations (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                major_name VARCHAR(255) NOT NULL,
                description TEXT,
                category VARCHAR(255),
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )
        ");
        echo "✅ Table created!\n";
    }
    
    // Clear existing data
    echo "🧹 Clearing existing data...\n";
    DB::table('major_recommendations')->truncate();
    
    // Insert sample data with correct categories
    echo "📝 Inserting sample data...\n";
    $sampleData = [
        [
            'major_name' => 'Seni Rupa',
            'description' => 'Program studi seni rupa dan desain',
            'category' => 'Humaniora',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'major_name' => 'Sosiologi',
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
            'major_name' => 'Teknik Informatika',
            'description' => 'Program studi teknik informatika',
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
            'major_name' => 'Psikologi',
            'description' => 'Program studi psikologi',
            'category' => 'Ilmu Sosial',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'major_name' => 'Teknik Mesin',
            'description' => 'Program studi teknik mesin',
            'category' => 'Ilmu Terapan',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'major_name' => 'Teknik Sipil',
            'description' => 'Program studi teknik sipil',
            'category' => 'Ilmu Terapan',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'major_name' => 'Kedokteran',
            'description' => 'Program studi kedokteran',
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
        ],
        [
            'major_name' => 'Sastra Indonesia',
            'description' => 'Program studi sastra',
            'category' => 'Humaniora',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'major_name' => 'Biologi',
            'description' => 'Program studi biologi',
            'category' => 'Ilmu Alam',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'major_name' => 'Geografi',
            'description' => 'Program studi geografi',
            'category' => 'Ilmu Alam',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]
    ];
    
    foreach ($sampleData as $data) {
        DB::table('major_recommendations')->insert($data);
    }
    
    echo "✅ Sample data inserted!\n";
    
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
    
    echo "\n✅ Database fix completed!\n";
    echo "🎯 Now you can test the rumpun ilmu filter!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
