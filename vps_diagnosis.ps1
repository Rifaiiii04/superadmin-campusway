# Script untuk mendiagnosis masalah data di VPS
Write-Host "=== VPS DIAGNOSIS SCRIPT ===" -ForegroundColor Green
Write-Host ""

# Informasi koneksi VPS
$vpsHost = "103.23.198.101"
$vpsUser = "marketing"
$vpsPassword = "##&21dq21)gZ"

Write-Host "VPS Information:" -ForegroundColor Yellow
Write-Host "Host: $vpsHost"
Write-Host "User: $vpsUser"
Write-Host ""

Write-Host "Commands to run on VPS:" -ForegroundColor Cyan
Write-Host ""

Write-Host "1. Connect to VPS:" -ForegroundColor White
Write-Host "ssh $vpsUser@$vpsHost" -ForegroundColor Gray
Write-Host ""

Write-Host "2. Navigate to project directory:" -ForegroundColor White
Write-Host "cd /var/www/superadmin/superadmin-campusway" -ForegroundColor Gray
Write-Host ""

Write-Host "3. Create debug script:" -ForegroundColor White
Write-Host "nano debug_production_data.php" -ForegroundColor Gray
Write-Host ""

Write-Host "4. Quick database test:" -ForegroundColor White
Write-Host "php artisan tinker" -ForegroundColor Gray
Write-Host ""

Write-Host "5. In tinker, run these commands:" -ForegroundColor White
Write-Host "DB::table('schools')->count();" -ForegroundColor Gray
Write-Host "DB::table('students')->count();" -ForegroundColor Gray
Write-Host "DB::table('major_recommendations')->count();" -ForegroundColor Gray
Write-Host "App\Models\School::count();" -ForegroundColor Gray
Write-Host "App\Models\Student::count();" -ForegroundColor Gray
Write-Host "App\Models\MajorRecommendation::count();" -ForegroundColor Gray
Write-Host "exit" -ForegroundColor Gray
Write-Host ""

Write-Host "6. Test API endpoints:" -ForegroundColor White
Write-Host "curl http://localhost/api/web/health" -ForegroundColor Gray
Write-Host "curl http://localhost/api/web/schools" -ForegroundColor Gray
Write-Host "curl http://localhost/api/web/majors" -ForegroundColor Gray
Write-Host ""

Write-Host "7. Check Laravel logs:" -ForegroundColor White
Write-Host "tail -f storage/logs/laravel.log" -ForegroundColor Gray
Write-Host ""

Write-Host "8. Clear cache:" -ForegroundColor White
Write-Host "php artisan cache:clear" -ForegroundColor Gray
Write-Host "php artisan config:clear" -ForegroundColor Gray
Write-Host "php artisan route:clear" -ForegroundColor Gray
Write-Host "php artisan view:clear" -ForegroundColor Gray
Write-Host ""

Write-Host "9. Check file permissions:" -ForegroundColor White
Write-Host "ls -la storage/" -ForegroundColor Gray
Write-Host "ls -la bootstrap/cache/" -ForegroundColor Gray
Write-Host ""

Write-Host "10. Check environment:" -ForegroundColor White
Write-Host "cat .env | grep DB_" -ForegroundColor Gray
Write-Host ""

Write-Host "=== COPY THIS DEBUG SCRIPT TO VPS ===" -ForegroundColor Red
Write-Host ""

$debugScript = @'
<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRODUCTION DIAGNOSIS ===\n\n";

try {
    // Test database connection
    echo "1. Database Connection Test:\n";
    $connection = \Illuminate\Support\Facades\DB::connection();
    $pdo = $connection->getPdo();
    echo "   ✓ Connected to: " . $connection->getDatabaseName() . "\n";
    echo "   ✓ Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n\n";

    // Check table counts
    echo "2. Table Data Counts:\n";
    $tables = ['schools', 'students', 'major_recommendations', 'questions', 'subjects'];
    foreach ($tables as $table) {
        try {
            $count = \Illuminate\Support\Facades\DB::table($table)->count();
            echo "   {$table}: {$count} records\n";
        } catch (Exception $e) {
            echo "   {$table}: ERROR - " . $e->getMessage() . "\n";
        }
    }
    echo "\n";

    // Test model queries
    echo "3. Model Query Tests:\n";
    try {
        $schools = \App\Models\School::count();
        echo "   School model: {$schools} records\n";
    } catch (Exception $e) {
        echo "   School model: ERROR - " . $e->getMessage() . "\n";
    }

    try {
        $students = \App\Models\Student::count();
        echo "   Student model: {$students} records\n";
    } catch (Exception $e) {
        echo "   Student model: ERROR - " . $e->getMessage() . "\n";
    }

    try {
        $majors = \App\Models\MajorRecommendation::count();
        echo "   Major model: {$majors} records\n";
    } catch (Exception $e) {
        echo "   Major model: ERROR - " . $e->getMessage() . "\n";
    }
    echo "\n";

    // Test API controller
    echo "4. API Controller Test:\n";
    try {
        $apiController = new \App\Http\Controllers\ApiController();
        $response = $apiController->getSchools();
        $data = json_decode($response->getContent(), true);
        echo "   API Schools: " . ($data['success'] ? 'SUCCESS' : 'FAILED') . "\n";
        echo "   Data count: " . count($data['data'] ?? []) . "\n";
    } catch (Exception $e) {
        echo "   API Schools: ERROR - " . $e->getMessage() . "\n";
    }
    echo "\n";

    // Check environment
    echo "5. Environment Check:\n";
    echo "   APP_ENV: " . env('APP_ENV') . "\n";
    echo "   APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "\n";
    echo "   DB_CONNECTION: " . env('DB_CONNECTION') . "\n";
    echo "   DB_HOST: " . env('DB_HOST') . "\n";
    echo "   DB_DATABASE: " . env('DB_DATABASE') . "\n";
    echo "\n";

    echo "=== DIAGNOSIS COMPLETE ===\n";

} catch (Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
'@

Write-Host $debugScript -ForegroundColor Yellow
Write-Host ""
Write-Host "=== END OF DEBUG SCRIPT ===" -ForegroundColor Red
Write-Host ""

Write-Host "Press any key to continue..." -ForegroundColor Green
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
