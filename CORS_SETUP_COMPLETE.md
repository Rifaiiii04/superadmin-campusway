# TKA SuperAdmin - CORS Setup Complete

## 🎯 Masalah yang Diperbaiki

**Problem**:

-   ❌ Next.js frontend tidak bisa melakukan API calls ke Laravel backend
-   ❌ CORS errors di browser console
-   ❌ Preflight requests tidak dihandle dengan benar

## 🔧 Perbaikan yang Dilakukan

### 1. ✅ Custom CORS Middleware

**File**: `app/Http/Middleware/Cors.php`

**Features**:

-   Handle preflight OPTIONS requests
-   Dynamic origin validation
-   Support untuk multiple allowed origins
-   Proper CORS headers untuk semua responses

**Allowed Origins**:

```php
$allowedOrigins = [
    'http://103.23.198.101',
    'http://localhost:3000',
    'http://127.0.0.1:3000',
    'http://localhost:3001',
    'http://127.0.0.1:3001',
];
```

**CORS Headers**:

-   `Access-Control-Allow-Origin`: Dynamic based on request origin
-   `Access-Control-Allow-Methods`: GET, POST, PUT, DELETE, OPTIONS
-   `Access-Control-Allow-Headers`: Content-Type, Authorization, X-Requested-With, Accept, Origin
-   `Access-Control-Allow-Credentials`: true
-   `Access-Control-Max-Age`: 3600

### 2. ✅ Middleware Registration

**File**: `app/Http/Kernel.php`

**Changes**:

```php
// Global API middleware
'api' => [
    \App\Http\Middleware\Cors::class,
    \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],

// Middleware aliases
'cors' => \App\Http\Middleware\Cors::class,
```

### 3. ✅ API Routes dengan CORS

**File**: `routes/api.php`

**Updated Routes**:

```php
// Public API Routes with CORS
Route::prefix('public')->middleware('cors')->group(function () {
    Route::get('/health', [ApiController::class, 'health']);
    Route::get('/schools', [ApiController::class, 'getSchools']);
    Route::get('/questions', [ApiController::class, 'getQuestions']);
    Route::get('/results', [ApiController::class, 'getResults']);
    Route::get('/majors', [ApiController::class, 'getMajors']);
    Route::get('/school-stats', [ApiController::class, 'getSchoolStats']);
    Route::post('/clear-cache', [ApiController::class, 'clearCache']);
});

// Student Web API Routes with CORS
Route::prefix('web')->middleware('cors')->group(function () {
    // Student registration, login, etc.
});

// School Dashboard API Routes with CORS
Route::prefix('school')->middleware('cors')->group(function () {
    // School authentication, dashboard, etc.
});
```

### 4. ✅ Test Script

**File**: `test-cors-setup.php`

**Tests**:

-   CORS headers dengan cURL
-   Preflight OPTIONS requests
-   Different origins validation
-   API endpoints functionality
-   Response format validation

## 🧪 Testing

### 1. Test CORS Setup

```bash
php test-cors-setup.php
```

**Tests**:

-   CORS headers present
-   Preflight requests working
-   Origin validation
-   API endpoints accessible
-   Response format correct

### 2. Manual Testing

1. **Browser Console Test**:

    - Open Next.js frontend
    - Check browser console for CORS errors
    - Verify API calls work

2. **cURL Test**:

    ```bash
    curl -H "Origin: http://103.23.198.101" \
         -H "Access-Control-Request-Method: GET" \
         -H "Access-Control-Request-Headers: Content-Type" \
         -X OPTIONS \
         http://103.23.198.101/super-admin/api/public/health
    ```

3. **API Endpoint Test**:
    ```bash
    curl -H "Origin: http://103.23.198.101" \
         -H "Accept: application/json" \
         http://103.23.198.101/super-admin/api/public/health
    ```

## 🚀 Deployment Steps

### 1. Upload Files to VPS

```bash
scp -r superadmin-backend/ user@103.23.198.101:/var/www/superadmin/
```

### 2. Clear Laravel Caches

```bash
cd /var/www/superadmin/superadmin-campusway
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 3. Test CORS Setup

```bash
php test-cors-setup.php
```

### 4. Restart Apache (if needed)

```bash
sudo systemctl restart apache2
```

## 🔍 Verification

### 1. Check CORS Headers

-   ✅ `Access-Control-Allow-Origin` header present
-   ✅ `Access-Control-Allow-Methods` includes required methods
-   ✅ `Access-Control-Allow-Headers` includes required headers
-   ✅ `Access-Control-Allow-Credentials` set to true

### 2. Test API Endpoints

-   ✅ `http://103.23.198.101/super-admin/api/public/health`
-   ✅ `http://103.23.198.101/super-admin/api/public/schools`
-   ✅ `http://103.23.198.101/super-admin/api/public/majors`
-   ✅ `http://103.23.198.101/super-admin/api/web/*`
-   ✅ `http://103.23.198.101/super-admin/api/school/*`

### 3. Test from Next.js

-   ✅ No CORS errors in browser console
-   ✅ API calls successful
-   ✅ Data loading properly
-   ✅ Error handling working

## 📊 Expected Results

### ✅ CORS Issues Resolved

-   No CORS errors in browser console
-   Preflight requests handled correctly
-   All origins properly validated
-   Credentials support working

### ✅ API Integration Working

-   Next.js can fetch data from Laravel
-   All API endpoints accessible
-   Proper JSON responses
-   Error handling working

### ✅ Performance Optimized

-   CORS headers cached for 1 hour
-   Preflight requests handled efficiently
-   No unnecessary CORS checks

## 🚨 Troubleshooting

### Issue 1: CORS headers not present

**Cause**: Middleware not applied to routes
**Solution**:

1. Check if CORS middleware is registered
2. Verify routes have CORS middleware
3. Clear route cache

### Issue 2: Preflight requests failing

**Cause**: OPTIONS method not handled
**Solution**:

1. Check CORS middleware handles OPTIONS
2. Verify Apache allows OPTIONS method
3. Test with cURL

### Issue 3: Origin not allowed

**Cause**: Origin not in allowed list
**Solution**:

1. Add origin to allowed list in CORS middleware
2. Check origin validation logic
3. Test with different origins

### Issue 4: Credentials not working

**Cause**: Credentials not enabled
**Solution**:

1. Check `Access-Control-Allow-Credentials` header
2. Verify credentials support in CORS middleware
3. Test with credentials in requests

## 🎉 Status: CORS SETUP COMPLETE

The CORS setup has been completed successfully:

-   ✅ Custom CORS middleware created
-   ✅ All API routes protected with CORS
-   ✅ Preflight requests handled
-   ✅ Multiple origins supported
-   ✅ Credentials support enabled
-   ✅ Testing scripts created

---

**Last Updated**: $(date)
**Version**: 1.0.0
**Status**: ✅ Complete
