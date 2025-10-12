<?php
// =====================================================
// Fix VPS Routes - Upload via HTTP
// =====================================================

echo "==========================================\n";
echo "Uploading routes/api.php to VPS\n";
echo "==========================================\n\n";

// Read the routes/api.php file
$routes_content = file_get_contents('routes/api.php');

if (!$routes_content) {
    echo "❌ Error: Cannot read routes/api.php\n";
    exit(1);
}

echo "✅ Read routes/api.php successfully\n";
echo "📄 File size: " . strlen($routes_content) . " bytes\n\n";

// Create a simple upload script for VPS
$upload_script = '<?php
// Upload routes/api.php content
$content = \'' . addslashes($routes_content) . '\';

$file_path = "/var/www/html/super-admin/routes/api.php";
$result = file_put_contents($file_path, $content);

if ($result !== false) {
    echo "✅ Successfully uploaded routes/api.php\n";
    echo "📄 File size: " . strlen($content) . " bytes\n";
    
    // Clear Laravel cache
    echo "🔧 Clearing Laravel cache...\n";
    exec("cd /var/www/html/super-admin && php artisan cache:clear");
    exec("cd /var/www/html/super-admin && php artisan config:clear");
    exec("cd /var/www/html/super-admin && php artisan route:clear");
    exec("cd /var/www/html/super-admin && php artisan view:clear");
    exec("cd /var/www/html/super-admin && php artisan optimize");
    
    // Set permissions
    exec("chown -R www-data:www-data /var/www/html/super-admin/storage /var/www/html/super-admin/bootstrap/cache");
    exec("chmod -R 775 /var/www/html/super-admin/storage /var/www/html/super-admin/bootstrap/cache");
    
    // Restart Apache
    exec("systemctl restart apache2");
    
    echo "✅ Cache cleared and Apache restarted\n";
    echo "🎉 Routes API fix complete!\n";
} else {
    echo "❌ Failed to upload routes/api.php\n";
}
?>';

// Save upload script
file_put_contents('upload_routes.php', $upload_script);

echo "📤 Created upload script: upload_routes.php\n";
echo "🔧 Run this on VPS:\n";
echo "1. Upload upload_routes.php to VPS\n";
echo "2. Run: php upload_routes.php\n\n";

echo "Or run these commands directly on VPS:\n";
echo "ssh root@103.23.198.101\n";
echo "cd /var/www/html/super-admin\n";
echo "wget http://103.23.198.101/super-admin/upload_routes.php\n";
echo "php upload_routes.php\n\n";

echo "==========================================\n";
echo "Manual Fix Commands for VPS:\n";
echo "==========================================\n";
echo "ssh root@103.23.198.101\n";
echo "cd /var/www/html/super-admin\n";
echo "php artisan cache:clear\n";
echo "php artisan config:clear\n";
echo "php artisan route:clear\n";
echo "php artisan view:clear\n";
echo "php artisan optimize\n";
echo "chown -R www-data:www-data storage bootstrap/cache\n";
echo "chmod -R 775 storage bootstrap/cache\n";
echo "systemctl restart apache2\n";
echo "php artisan route:list | grep api\n";
?>
