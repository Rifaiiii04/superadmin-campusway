<?php
// =====================================================
// Web Update Routes API - Access via Browser
// =====================================================

// New routes/api.php content
$new_routes_content = '<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\StudentWebController;
use App\Http\Controllers\SchoolAuthController;
use App\Http\Controllers\SchoolDashboardController;
use App\Http\Controllers\TkaScheduleController;

// ===========================================
// STUDENT WEB API ROUTES (Public - No Auth)
// ===========================================
Route::prefix("web")->group(function () {
    // Public endpoints
    Route::get("/schools", [StudentWebController::class, "getSchools"]);
    Route::get("/questions", [ApiController::class, "getQuestions"]);
    Route::get("/majors", [StudentWebController::class, "getMajors"]);
    Route::get("/majors/{id}", [StudentWebController::class, "getMajorDetails"]);
    Route::get("/health", [ApiController::class, "healthCheck"]);
    
    // Student authentication
    Route::post("/register-student", [StudentWebController::class, "register"]);
    Route::post("/login", [StudentWebController::class, "login"]);
    
    // Student major selection (no auth required for simplicity)
    Route::post("/choose-major", [StudentWebController::class, "chooseMajor"]);
    Route::post("/change-major", [StudentWebController::class, "changeMajor"]);
    Route::get("/student-choice/{studentId}", [StudentWebController::class, "getStudentChoice"]);
    Route::get("/major-status/{studentId}", [StudentWebController::class, "checkMajorStatus"]);
    Route::get("/student-profile/{studentId}", [StudentWebController::class, "getStudentProfile"]);
    
    // TKA Schedules for students
    Route::get("/tka-schedules", [TkaScheduleController::class, "index"]);
    Route::get("/tka-schedules/upcoming", [TkaScheduleController::class, "upcoming"]);
});

// ===========================================
// SCHOOL DASHBOARD API ROUTES (With Auth)
// ===========================================
Route::prefix("school")->group(function () {
    // School authentication
    Route::post("/login", [SchoolAuthController::class, "login"]);
    Route::post("/logout", [SchoolAuthController::class, "logout"])->middleware("auth:sanctum");
    
    // Protected routes (require authentication)
    Route::middleware("auth:sanctum")->group(function () {
        Route::get("/profile", [SchoolAuthController::class, "profile"]);
        Route::get("/dashboard", [SchoolDashboardController::class, "index"]);
        Route::get("/students", [SchoolDashboardController::class, "students"]);
        Route::get("/students/{id}", [SchoolDashboardController::class, "studentDetail"]);
        Route::get("/major-statistics", [SchoolDashboardController::class, "majorStatistics"]);
        Route::get("/export-students", [SchoolDashboardController::class, "exportStudents"]);
        Route::get("/students-without-choice", [SchoolDashboardController::class, "studentsWithoutChoice"]);
        
        // Student management
        Route::post("/students", [SchoolDashboardController::class, "storeStudent"]);
        Route::put("/students/{id}", [SchoolDashboardController::class, "updateStudent"]);
        Route::delete("/students/{id}", [SchoolDashboardController::class, "deleteStudent"]);
        
        // Import students
        Route::post("/import-students", [SchoolDashboardController::class, "importStudents"]);
        Route::get("/import-template", [SchoolDashboardController::class, "downloadTemplate"]);
        Route::get("/import-rules", [SchoolDashboardController::class, "importRules"]);
        
        // Classes
        Route::get("/classes", [SchoolDashboardController::class, "classes"]);
        
        // TKA Schedules
        Route::get("/tka-schedules", [TkaScheduleController::class, "index"]);
        Route::get("/tka-schedules/upcoming", [TkaScheduleController::class, "upcoming"]);
    });
});

// ===========================================
// PUBLIC API ROUTES (SuperAdmin Integration)
// ===========================================
Route::prefix("public")->group(function () {
    Route::get("/schools", [ApiController::class, "getSchools"]);
    Route::get("/questions", [ApiController::class, "getQuestions"]);
    Route::get("/majors", [ApiController::class, "getMajors"]);
    Route::get("/health", [ApiController::class, "healthCheck"]);
});

// ===========================================
// EXPORT ROUTES (No middleware)
// ===========================================
Route::get("/major-recommendations/export", [App\Http\Controllers\MajorRecommendationController::class, "export"]);

// ===========================================
// TKA SCHEDULES PUBLIC ROUTES
// ===========================================
Route::get("/tka-schedules", [TkaScheduleController::class, "index"]);
Route::get("/tka-schedules/upcoming", [TkaScheduleController::class, "upcoming"]);
';

// Try to find Laravel app directory
$possible_paths = [
    '/var/www/html/super-admin',
    '/var/www/super-admin',
    '/home/marketing/super-admin',
    '/home/marketing/public/super-admin',
    '/opt/super-admin',
    '/usr/share/nginx/html/super-admin'
];

$laravel_path = null;
foreach ($possible_paths as $path) {
    if (file_exists($path . '/artisan')) {
        $laravel_path = $path;
        break;
    }
}

if (!$laravel_path) {
    echo "<h1>❌ Laravel App Not Found</h1>";
    echo "<p>Could not find Laravel application directory.</p>";
    echo "<p>Please manually upload the routes/api.php file to your Laravel app.</p>";
    echo "<h2>New routes/api.php content:</h2>";
    echo "<pre>" . htmlspecialchars($new_routes_content) . "</pre>";
    exit;
}

$routes_file = $laravel_path . '/routes/api.php';

// Backup existing file
if (file_exists($routes_file)) {
    $backup_file = $routes_file . '.backup.' . date('Y-m-d_H-i-s');
    copy($routes_file, $backup_file);
    echo "<p>✅ Backed up existing routes to: " . basename($backup_file) . "</p>";
}

// Write new routes
$result = file_put_contents($routes_file, $new_routes_content);

if ($result !== false) {
    echo "<h1>✅ Routes API Updated Successfully!</h1>";
    echo "<p>📄 File size: " . strlen($new_routes_content) . " bytes</p>";
    echo "<p>📍 Updated: " . $routes_file . "</p>";
    
    // Clear Laravel cache
    echo "<h2>🔧 Clearing Laravel Cache...</h2>";
    
    $commands = [
        "cd $laravel_path && php artisan cache:clear",
        "cd $laravel_path && php artisan config:clear", 
        "cd $laravel_path && php artisan route:clear",
        "cd $laravel_path && php artisan view:clear",
        "cd $laravel_path && php artisan optimize"
    ];
    
    foreach ($commands as $cmd) {
        $output = shell_exec($cmd . ' 2>&1');
        echo "<p>Running: $cmd</p>";
        if ($output) {
            echo "<pre>" . htmlspecialchars($output) . "</pre>";
        }
    }
    
    echo "<h2>🎉 Update Complete!</h2>";
    echo "<p>Test the endpoints:</p>";
    echo "<ul>";
    echo "<li><a href='/super-admin/api/web/health' target='_blank'>Health Check</a></li>";
    echo "<li><a href='/super-admin/api/web/schools' target='_blank'>Schools List</a></li>";
    echo "<li><a href='/super-admin/api/web/majors' target='_blank'>Majors List</a></li>";
    echo "<li><a href='/super-admin/api/public/health' target='_blank'>Public Health Check</a></li>";
    echo "</ul>";
    
} else {
    echo "<h1>❌ Failed to Update Routes</h1>";
    echo "<p>Could not write to: " . $routes_file . "</p>";
    echo "<p>Please check file permissions.</p>";
}
?>
