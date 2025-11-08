# Panduan Cek Konfigurasi Apache

## 1. Cek Apache Error Log
```bash
sudo tail -f /var/log/apache2/error.log
```

## 2. Cek Apache Virtual Host Configuration
```bash
# Cek virtual host yang aktif
sudo apache2ctl -S

# Cek konfigurasi virtual host
sudo cat /etc/apache2/sites-available/*.conf | grep -A 20 "super-admin"
```

## 3. Cek Apache Modules
```bash
# Cek apakah mod_rewrite aktif
sudo apache2ctl -M | grep rewrite

# Cek apakah mod_headers aktif
sudo apache2ctl -M | grep headers
```

## 4. Test Route Tanpa Middleware
Test endpoint baru yang tidak memerlukan auth:
```bash
curl http://103.23.198.101/super-admin/api/school/test-student-detail/40
```

Jika ini berfungsi, berarti masalahnya di middleware atau di method studentDetail.

## 5. Cek .htaccess
```bash
cat public/.htaccess
```

## 6. Cek Laravel Log
```bash
tail -f storage/logs/laravel.log
tail -f storage/logs/student_detail_debug.log
```

## 7. Test dengan artisan serve (bypass Apache)
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Lalu test:
```bash
curl http://103.23.198.101:8000/api/school/test-student-detail/40
```

Jika ini berfungsi, berarti masalahnya di konfigurasi Apache.

## 8. Cek Permission
```bash
# Cek permission untuk storage/logs
ls -la storage/logs/
chmod -R 775 storage/logs/
chown -R www-data:www-data storage/logs/
```

## 9. Cek PHP Error
```bash
# Cek PHP error log
sudo tail -f /var/log/php*-fpm.log

# Atau cek php.ini untuk error_log location
php -i | grep error_log
```

