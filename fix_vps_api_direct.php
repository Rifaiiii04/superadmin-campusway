<?php
// =====================================================
// Fix VPS API 404 Error - Direct Fix
// =====================================================

echo "==========================================\n";
echo "Fixing VPS API 404 Error\n";
echo "==========================================\n\n";

// VPS Configuration
$vps_user = "root";
$vps_ip = "103.23.198.101";
$vps_path = "/var/www/html/super-admin";

echo "🔧 Step 1: Clearing Laravel Cache on VPS...\n";

$commands = [
    "cd $vps_path",
    "php artisan cache:clear",
    "php artisan config:clear", 
    "php artisan route:clear",
    "php artisan view:clear",
    "php artisan optimize",
    "chown -R www-data:www-data storage bootstrap/cache",
    "chmod -R 775 storage bootstrap/cache",
    "systemctl restart apache2"
];

$command_string = implode(" && ", $commands);

echo "Running: ssh $vps_user@$vps_ip \"$command_string\"\n\n";

// Execute via SSH
$ssh_command = "ssh $vps_user@$vps_ip \"$command_string\"";
$output = shell_exec($ssh_command);

echo "Output:\n";
echo $output . "\n";

echo "==========================================\n";
echo "✅ Fix Complete!\n";
echo "==========================================\n\n";

echo "Testing Endpoints:\n";
echo "1. Health Check: http://103.23.198.101/super-admin/api/web/health\n";
echo "2. Schools: http://103.23.198.101/super-admin/api/web/schools\n";
echo "3. Majors: http://103.23.198.101/super-admin/api/web/majors\n";
echo "4. School Login: http://103.23.198.101/super-admin/api/school/login\n";
echo "5. Student Register: http://103.23.198.101/super-admin/api/web/register-student\n\n";

echo "Test dengan curl:\n";
echo "curl http://103.23.198.101/super-admin/api/web/health\n";
echo "curl http://103.23.198.101/super-admin/api/web/schools\n";
echo "curl http://103.23.198.101/super-admin/api/web/majors\n\n";

echo "Jika masih 404, cek:\n";
echo "1. Apache error logs: tail -f /var/log/apache2/error.log\n";
echo "2. Laravel logs: tail -f /var/www/html/super-admin/storage/logs/laravel.log\n";
echo "3. Routes: php artisan route:list | grep api\n";
?>
