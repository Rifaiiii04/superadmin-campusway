<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\School;
use App\Models\MajorRecommendation;

echo "=== FIXING DATABASE CONNECTION ===\n\n";

try {
    // Test current connection
    echo "1. Testing current database connection...\n";
    $pdo = DB::connection()->getPdo();
    echo "   ✅ Database connected!\n";
    echo "   Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
    echo "   Database: " . $pdo->query('SELECT DATABASE()')->fetchColumn() . "\n";
    
    // Test all models
    echo "\n2. Testing all models...\n";
    
    // Students
    $studentsCount = Student::count();
    echo "   Students: " . $studentsCount . "\n";
    
    // Schools
    $schoolsCount = School::count();
    echo "   Schools: " . $schoolsCount . "\n";
    
    // Major Recommendations
    $majorsCount = MajorRecommendation::count();
    echo "   Major Recommendations: " . $majorsCount . "\n";
    
    // Test pagination
    echo "\n3. Testing pagination...\n";
    $students = Student::with(['school'])->paginate(10);
    echo "   Students pagination total: " . $students->total() . "\n";
    echo "   Students pagination data count: " . $students->count() . "\n";
    
    // Test controller data
    echo "\n4. Testing controller data structure...\n";
    $schools = School::select('id', 'name')->get();
    echo "   Schools for dropdown: " . $schools->count() . "\n";
    
    // Test Inertia data structure
    echo "\n5. Testing Inertia data structure...\n";
    $inertiaData = [
        'title' => 'Manajemen Siswa',
        'students' => $students,
        'schools' => $schools,
        'debug' => [
            'total_students' => $studentsCount,
            'pagination_total' => $students->total(),
            'current_page' => $students->currentPage(),
            'per_page' => $students->perPage(),
            'schools_count' => $schools->count()
        ]
    ];
    
    echo "   Inertia data keys: " . implode(', ', array_keys($inertiaData)) . "\n";
    echo "   Students data type: " . gettype($inertiaData['students']) . "\n";
    echo "   Students data keys: " . implode(', ', array_keys($inertiaData['students']->toArray())) . "\n";
    
    echo "\n✅ Database connection and data structure are working correctly!\n";
    echo "The issue might be in the frontend or route configuration.\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
