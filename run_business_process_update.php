<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database configuration
$config = [
    'driver' => 'sqlsrv',
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_DATABASE'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];

// Initialize Capsule
$capsule = new Capsule;
$capsule->addConnection($config);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🚀 Starting Business Process Update...\n\n";

try {
    // 1. Update subjects table structure
    echo "📚 Step 1: Updating subjects table structure...\n";
    
    if (!Capsule::schema()->hasColumn('subjects', 'subject_number')) {
        Capsule::schema()->table('subjects', function (Blueprint $table) {
            $table->string('subject_number')->nullable()->after('code');
        });
        echo "✅ Added subject_number column\n";
    }
    
    if (!Capsule::schema()->hasColumn('subjects', 'type')) {
        Capsule::schema()->table('subjects', function (Blueprint $table) {
            $table->enum('type', ['wajib', 'pilihan'])->default('pilihan')->after('is_required');
        });
        echo "✅ Added type column\n";
    }

    // 2. Update major_recommendations table
    echo "\n📋 Step 2: Updating major_recommendations table...\n";
    
    if (Capsule::schema()->hasColumn('major_recommendations', 'category')) {
        // First, check and drop any indexes on the category column
        try {
            // Check if index exists
            $indexExists = Capsule::select("
                SELECT COUNT(*) as count 
                FROM sys.indexes 
                WHERE object_id = OBJECT_ID('major_recommendations') 
                AND name LIKE '%category%'
            ");
            
            if ($indexExists[0]->count > 0) {
                // Drop all indexes that contain 'category' in the name
                Capsule::statement("
                    DECLARE @sql NVARCHAR(MAX) = '';
                    SELECT @sql += 'DROP INDEX ' + name + ' ON major_recommendations;'
                    FROM sys.indexes 
                    WHERE object_id = OBJECT_ID('major_recommendations') 
                    AND name LIKE '%category%';
                    EXEC(@sql);
                ");
                echo "✅ Dropped category indexes\n";
            } else {
                echo "⚠️ No category indexes found\n";
            }
        } catch (Exception $e) {
            echo "⚠️ Error checking/dropping indexes: " . $e->getMessage() . "\n";
        }
        
        // Then drop the column
        try {
            Capsule::schema()->table('major_recommendations', function (Blueprint $table) {
                $table->dropColumn('category');
            });
            echo "✅ Removed old category column\n";
        } catch (Exception $e) {
            echo "❌ Error dropping category column: " . $e->getMessage() . "\n";
            echo "Trying alternative approach...\n";
            
            // Alternative: Use raw SQL
            try {
                Capsule::statement("ALTER TABLE major_recommendations DROP COLUMN category");
                echo "✅ Removed old category column (alternative method)\n";
            } catch (Exception $e2) {
                echo "❌ Alternative method also failed: " . $e2->getMessage() . "\n";
                echo "⚠️ Continuing without dropping category column...\n";
            }
        }
    }
    
    if (!Capsule::schema()->hasColumn('major_recommendations', 'rumpun_ilmu')) {
        Capsule::schema()->table('major_recommendations', function (Blueprint $table) {
            $table->enum('rumpun_ilmu', [
                'HUMANIORA', 
                'ILMU SOSIAL', 
                'ILMU ALAM', 
                'ILMU FORMAL', 
                'ILMU TERAPAN'
            ])->after('major_name')->default('ILMU ALAM');
        });
        echo "✅ Added rumpun_ilmu column\n";
    }

    // 3. Clear and insert new subjects data
    echo "\n📖 Step 3: Updating subjects data...\n";
    
    Capsule::table('subjects')->truncate();
    echo "✅ Cleared existing subjects data\n";
    
    $subjects = [
        // Mata Uji Wajib (3)
        ['subject_number' => '1', 'name' => 'Matematika Lanjutan', 'code' => 'MTK_L', 'type' => 'wajib', 'is_required' => true, 'is_active' => true],
        ['subject_number' => '2', 'name' => 'Bahasa Indonesia Lanjutan', 'code' => 'BIN_L', 'type' => 'wajib', 'is_required' => true, 'is_active' => true],
        ['subject_number' => '3', 'name' => 'Bahasa Inggris Lanjutan', 'code' => 'BIG_L', 'type' => 'wajib', 'is_required' => true, 'is_active' => true],
        
        // Mata Uji Pilihan (16)
        ['subject_number' => '4', 'name' => 'Fisika', 'code' => 'FIS', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
        ['subject_number' => '5', 'name' => 'Kimia', 'code' => 'KIM', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
        ['subject_number' => '6', 'name' => 'Biologi', 'code' => 'BIO', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
        ['subject_number' => '7', 'name' => 'Ekonomi', 'code' => 'EKO', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
        ['subject_number' => '8', 'name' => 'Sosiologi', 'code' => 'SOS', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
        ['subject_number' => '9', 'name' => 'Geografi', 'code' => 'GEO', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
        ['subject_number' => '10', 'name' => 'Sejarah', 'code' => 'SEJ', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
        ['subject_number' => '11', 'name' => 'Antropologi', 'code' => 'ANT', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
        ['subject_number' => '12', 'name' => 'PPKn/Pendidikan Pancasila', 'code' => 'PPKN', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
        ['subject_number' => '13', 'name' => 'Bahasa Arab', 'code' => 'BAR', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
        ['subject_number' => '14', 'name' => 'Bahasa Jerman', 'code' => 'BJE', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
        ['subject_number' => '15', 'name' => 'Bahasa Prancis', 'code' => 'BPR', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
        ['subject_number' => '16', 'name' => 'Bahasa Jepang', 'code' => 'BJP', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
        ['subject_number' => '17', 'name' => 'Bahasa Korea', 'code' => 'BKO', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
        ['subject_number' => '18', 'name' => 'Bahasa Mandarin', 'code' => 'BMA', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
        ['subject_number' => '19', 'name' => 'Produk/Projek Kreatif dan Kewirausahaan', 'code' => 'PKK', 'type' => 'pilihan', 'is_required' => false, 'is_active' => true],
    ];

    foreach ($subjects as $subject) {
        Capsule::table('subjects')->insert([
            'subject_number' => $subject['subject_number'],
            'name' => $subject['name'],
            'code' => $subject['code'],
            'type' => $subject['type'],
            'is_required' => $subject['is_required'],
            'is_active' => $subject['is_active'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    echo "✅ Inserted " . count($subjects) . " subjects\n";

    // 4. Update existing major recommendations
    echo "\n🎓 Step 4: Updating existing major recommendations...\n";
    
    $updated = Capsule::table('major_recommendations')
        ->where('rumpun_ilmu', 'ILMU ALAM')
        ->orWhere('rumpun_ilmu', null)
        ->update(['rumpun_ilmu' => 'ILMU ALAM']);
    echo "✅ Updated {$updated} majors to ILMU ALAM\n";

    // 5. Create rumpun_ilmu table
    echo "\n🏛️ Step 5: Creating rumpun_ilmu table...\n";
    
    if (!Capsule::schema()->hasTable('rumpun_ilmu')) {
        Capsule::schema()->create('rumpun_ilmu', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        echo "✅ Created rumpun_ilmu table\n";
    }

    // Insert rumpun ilmu data
    $rumpunIlmu = [
        ['name' => 'HUMANIORA', 'description' => 'Rumpun ilmu yang mempelajari manusia, budaya, dan ekspresi manusia', 'is_active' => true],
        ['name' => 'ILMU SOSIAL', 'description' => 'Rumpun ilmu yang mempelajari perilaku manusia dalam masyarakat', 'is_active' => true],
        ['name' => 'ILMU ALAM', 'description' => 'Rumpun ilmu yang mempelajari fenomena alam dan hukum-hukum alam', 'is_active' => true],
        ['name' => 'ILMU FORMAL', 'description' => 'Rumpun ilmu yang mempelajari sistem formal dan abstrak', 'is_active' => true],
        ['name' => 'ILMU TERAPAN', 'description' => 'Rumpun ilmu yang menerapkan pengetahuan untuk memecahkan masalah praktis', 'is_active' => true],
    ];

    Capsule::table('rumpun_ilmu')->insert($rumpunIlmu);
    echo "✅ Inserted " . count($rumpunIlmu) . " rumpun ilmu\n";

    echo "\n🎉 Business Process Update Completed Successfully!\n";
    echo "\n📊 Summary:\n";
    echo "- Updated subjects table with 19 subjects (3 wajib, 16 pilihan)\n";
    echo "- Updated major_recommendations table with rumpun_ilmu column\n";
    echo "- Created rumpun_ilmu table with 5 categories\n";
    echo "- Updated existing data to use new structure\n";
    echo "\n✅ System is now ready with the new business process structure!\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
