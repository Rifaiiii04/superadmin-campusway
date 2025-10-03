<?php

// Cleanup test files
echo "Cleaning up test files...\n\n";

$testFiles = [
    'test_frontend_html.html',
    'test_frontend_access.php',
    'test_server_status.php',
    'test_frontend_exact_url.php',
    'test_cors_and_access.php',
    'test_frontend_url.php',
    'test_student_choice_endpoint.php',
    'test_tka_schedules_route.php',
    'test_fixed_student_login.php',
    'fix_all_student_status.php',
    'debug_student_status.php',
    'fix_student_passwords.php',
    'test_student_login.php',
    'test_post_method.php',
    'test_put_final.php',
    'fix_all_constraints.php',
    'fix_foreign_keys.php',
    'fix_mysql_auto_increment.php',
    'fix_mysql_constraints.php',
    'test_admin_fixed.php',
    'test_all_crud.php',
    'test_api_endpoint.php',
    'test_direct_update.php',
    'test_final_crud.php',
    'reset_admin_password.php',
    'test_admin.php',
    'test_csrf.php',
    'test_csrf_fixed.php',
    'test_put_endpoint.php',
    'test_school_endpoints.php',
    'test_simple.php',
    'add_major_descriptions.php',
    'cleanup_test_files.php'
];

$deleted = 0;
$notFound = 0;

foreach ($testFiles as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "✅ Deleted: {$file}\n";
            $deleted++;
        } else {
            echo "❌ Failed to delete: {$file}\n";
        }
    } else {
        $notFound++;
    }
}

echo "\n=== Summary ===\n";
echo "Deleted: {$deleted} files\n";
echo "Not found: {$notFound} files\n";

echo "\nCleanup completed.\n";
