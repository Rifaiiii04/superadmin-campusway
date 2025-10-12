# API Endpoint Fix Summary - TKA Web System

## 🎯 Masalah yang Diperbaiki

### Masalah Utama
**Routes untuk StudentWebController tidak terdaftar di `routes/api.php`**, sehingga frontend tidak bisa mengakses endpoint API yang dibutuhkan.

### Dampak
- Frontend `tka-frontend-siswa` tidak bisa melakukan:
  - Login siswa
  - Registrasi siswa
  - Memilih jurusan
  - Melihat daftar jurusan
  - Mendapatkan jadwal TKA
  
- Frontend school dashboard tidak bisa mengakses API dengan benar

---

## ✅ Perbaikan yang Dilakukan

### 1. **Routes API (`routes/api.php`)**

#### A. Student Web API (Public - No Auth)
Menambahkan routes untuk endpoint `/api/web`:

```php
Route::prefix('web')->group(function () {
    // Authentication
    Route::post('/register-student', [StudentWebController::class, 'register']);
    Route::post('/login', [StudentWebController::class, 'login']);
    
    // Schools & Majors
    Route::get('/schools', [StudentWebController::class, 'getSchools']);
    Route::get('/majors', [StudentWebController::class, 'getMajors']);
    Route::get('/majors/{id}', [StudentWebController::class, 'getMajorDetails']);
    
    // Student Major Selection
    Route::post('/choose-major', [StudentWebController::class, 'chooseMajor']);
    Route::post('/change-major', [StudentWebController::class, 'changeMajor']);
    Route::get('/student-choice/{studentId}', [StudentWebController::class, 'getStudentChoice']);
    Route::get('/major-status/{studentId}', [StudentWebController::class, 'checkMajorStatus']);
    Route::get('/student-profile/{studentId}', [StudentWebController::class, 'getStudentProfile']);
    
    // TKA Schedules
    Route::get('/tka-schedules', [TkaScheduleController::class, 'index']);
    Route::get('/tka-schedules/upcoming', [TkaScheduleController::class, 'upcoming']);
});
```

#### B. School Dashboard API (With Auth)
Menambahkan routes untuk endpoint `/api/school`:

```php
Route::prefix('school')->group(function () {
    // Authentication
    Route::post('/login', [SchoolAuthController::class, 'login']);
    Route::post('/logout', [SchoolAuthController::class, 'logout']);
    
    // Protected endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/dashboard', [SchoolDashboardController::class, 'index']);
        Route::get('/students', [SchoolDashboardController::class, 'students']);
        Route::get('/export-students', [SchoolDashboardController::class, 'exportStudents']);
        // ... dan lain-lain
    });
});
```

#### C. Public API (SuperAdmin Integration)
Menambahkan routes untuk endpoint `/api/public`:

```php
Route::prefix('public')->group(function () {
    Route::get('/schools', [ApiController::class, 'getSchools']);
    Route::get('/questions', [ApiController::class, 'getQuestions']);
    Route::get('/majors', [ApiController::class, 'getMajors']);
    Route::get('/health', [ApiController::class, 'healthCheck']);
});
```

### 2. **TkaScheduleController Enhancement**

Menambahkan method `upcoming()` dan mengupdate `index()` untuk mendukung JSON response:

```php
public function index(Request $request)
{
    // Check if API request
    if ($request->wantsJson() || $request->is('api/*')) {
        // Return JSON response
        $schedules = TkaSchedule::where('is_active', true)->get();
        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }
    
    // Return Inertia for web
    return Inertia::render('SuperAdmin/TkaSchedules', [...]);
}

public function upcoming(Request $request)
{
    $schedules = TkaSchedule::where('is_active', true)
        ->where('end_date', '>=', now())
        ->orderBy('start_date', 'asc')
        ->get();
    
    return response()->json([
        'success' => true,
        'data' => $schedules
    ]);
}
```

### 3. **CORS Configuration (`config/cors.php`)**

Mengupdate konfigurasi CORS untuk mendukung lebih banyak origins:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie', 'super-admin/api/*'],

'allowed_origins' => [
    'http://103.23.198.101',
    'http://103.23.198.101:3000',
    'http://localhost:3000',
    'http://127.0.0.1:3000',
    'http://10.112.234.213:3000',
    'http://10.112.234.213',
],

'allowed_origins_patterns' => [
    '/^http:\/\/10\.\d{1,3}\.\d{1,3}\.\d{1,3}(:\d+)?$/',
    '/^http:\/\/192\.168\.\d{1,3}\.\d{1,3}(:\d+)?$/',
],

'exposed_headers' => ['Content-Disposition', 'Content-Type', 'Content-Length', 'Authorization'],
```

---

## 📁 File yang Diubah

1. ✅ `routes/api.php` - Menambahkan semua routes yang dibutuhkan
2. ✅ `app/Http/Controllers/TkaScheduleController.php` - Menambahkan method `upcoming()` dan update `index()`
3. ✅ `config/cors.php` - Update CORS configuration
4. ✅ `API_ENDPOINTS_PRODUCTION.md` - Dokumentasi endpoint lengkap
5. ✅ `deploy_api_fixes.sh` - Script deployment ke production
6. ✅ `test_api_endpoints_production.sh` - Script testing endpoint

---

## 🚀 Cara Deploy ke Production

### 1. Persiapan
```bash
cd /path/to/superadmin-backend
chmod +x deploy_api_fixes.sh
chmod +x test_api_endpoints_production.sh
```

### 2. Deploy
```bash
./deploy_api_fixes.sh
```

Script akan:
1. Upload `routes/api.php` ke VPS
2. Upload `TkaScheduleController.php` ke VPS
3. Upload `config/cors.php` ke VPS
4. Clear Laravel cache
5. Set permissions
6. Restart Apache

### 3. Test Endpoints
```bash
./test_api_endpoints_production.sh
```

Atau test manual dengan curl:
```bash
# Health check
curl http://103.23.198.101/super-admin/api/web/health

# Get schools
curl http://103.23.198.101/super-admin/api/web/schools

# Get majors
curl http://103.23.198.101/super-admin/api/web/majors

# Test student login
curl -X POST http://103.23.198.101/super-admin/api/web/login \
  -H "Content-Type: application/json" \
  -d '{"nisn":"1234567890","password":"password"}'
```

---

## 🔧 Frontend Configuration

### Update Environment Variables

File: `tka-frontend-siswa/.env.local` atau `.env.production`

```env
# Production API URLs
NEXT_PUBLIC_API_BASE_URL=http://103.23.198.101/super-admin/api/school
NEXT_PUBLIC_STUDENT_API_BASE_URL=http://103.23.198.101/super-admin/api/web
NEXT_PUBLIC_SUPERADMIN_API_URL=http://103.23.198.101/super-admin/api
```

### Verifikasi Frontend API Calls

File: `tka-frontend-siswa/src/services/api.ts` sudah benar dan akan menggunakan endpoint yang baru.

Mapping yang sudah match:
- ✅ `studentApiService.login()` → `POST /api/web/login`
- ✅ `studentApiService.register()` → `POST /api/web/register-student`
- ✅ `studentApiService.getMajors()` → `GET /api/web/majors`
- ✅ `studentApiService.chooseMajor()` → `POST /api/web/choose-major`
- ✅ `studentApiService.getTkaSchedules()` → `GET /api/web/tka-schedules`
- ✅ Dan semua endpoint lainnya...

---

## ✅ Checklist Deployment

### Backend
- [x] Routes API sudah ditambahkan
- [x] Controller methods sudah lengkap
- [x] CORS configuration sudah diupdate
- [x] Dokumentasi API sudah dibuat
- [ ] Deploy ke production VPS
- [ ] Test endpoint dengan curl/Postman
- [ ] Clear Laravel cache di production
- [ ] Restart Apache di production

### Frontend
- [ ] Update environment variables
- [ ] Test API connection
- [ ] Test student login
- [ ] Test major selection
- [ ] Test TKA schedules
- [ ] Build production frontend
- [ ] Deploy frontend

### Monitoring
- [ ] Check Laravel logs: `/var/www/html/super-admin/storage/logs/laravel.log`
- [ ] Check Apache error logs: `/var/log/apache2/error.log`
- [ ] Monitor API response times
- [ ] Test with real users

---

## 🐛 Troubleshooting

### Jika endpoint mengembalikan 404
```bash
# Clear route cache
ssh root@103.23.198.101
cd /var/www/html/super-admin
php artisan route:clear
php artisan cache:clear
php artisan config:clear
```

### Jika ada CORS error
```bash
# Verify CORS config
ssh root@103.23.198.101
cd /var/www/html/super-admin
cat config/cors.php

# Restart Apache
systemctl restart apache2
```

### Jika endpoint mengembalikan 500
```bash
# Check logs
ssh root@103.23.198.101
tail -f /var/www/html/super-admin/storage/logs/laravel.log
```

### Jika controller not found
```bash
# Re-upload controller dan clear cache
scp app/Http/Controllers/TkaScheduleController.php root@103.23.198.101:/var/www/html/super-admin/app/Http/Controllers/
ssh root@103.23.198.101 "cd /var/www/html/super-admin && php artisan optimize"
```

---

## 📊 Endpoint Production URLs

### Student Web API
```
http://103.23.198.101/super-admin/api/web/schools
http://103.23.198.101/super-admin/api/web/majors
http://103.23.198.101/super-admin/api/web/tka-schedules
http://103.23.198.101/super-admin/api/web/login (POST)
http://103.23.198.101/super-admin/api/web/register-student (POST)
http://103.23.198.101/super-admin/api/web/choose-major (POST)
```

### School Dashboard API
```
http://103.23.198.101/super-admin/api/school/login (POST)
http://103.23.198.101/super-admin/api/school/dashboard (GET - Auth required)
http://103.23.198.101/super-admin/api/school/students (GET - Auth required)
```

### Public API
```
http://103.23.198.101/super-admin/api/public/schools
http://103.23.198.101/super-admin/api/public/majors
http://103.23.198.101/super-admin/api/public/health
```

---

## 📝 Notes

1. **Authentication**: School dashboard menggunakan Laravel Sanctum (Bearer token)
2. **Student API**: Tidak memerlukan authentication (public access)
3. **CORS**: Sudah dikonfigurasi untuk localhost, production, dan network access
4. **Error Handling**: Semua endpoint mengembalikan format JSON yang konsisten
5. **Logging**: Error di-log ke `storage/logs/laravel.log`

---

## 🎉 Selesai!

Semua endpoint API sudah diperbaiki dan siap digunakan di production. Lakukan deployment dan testing untuk memastikan semua berfungsi dengan baik.

**Developed by:** TKA Web Development Team  
**Last Updated:** October 11, 2025

