# TKA Super Admin - Admin Model Update Summary

## 🎯 Perubahan yang Dilakukan

**Request**: Update `app/Models/Admin.php` untuk menggunakan `name`, `email`, dan `password` fields dengan guard `admin`.

## 🔧 Perubahan yang Dilakukan

### 1. ✅ Admin Model Updated

**File**: `app/Models/Admin.php`

**Before**:

```php
class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
    ];
}
```

**After**:

```php
class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];
}
```

**Key Changes**:

-   ✅ Added `protected $guard = 'admin';`
-   ✅ Changed fillable fields from `username` to `name`, `email`, `password`
-   ✅ Removed `HasApiTokens` trait
-   ✅ Removed `$casts` for password hashing (Laravel 11+ handles this automatically)

### 2. ✅ Controller Updated

**File**: `app/Http/Controllers/SuperAdminController.php`

**Changes**:

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',  // Changed from username
        'password' => 'required',
    ]);

    if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',  // Changed from username
    ])->withInput($request->only('email'));
}
```

### 3. ✅ Login Form Updated

**File**: `resources/js/Pages/SuperAdmin/Login.jsx`

**Changes**:

```javascript
// Form data
const { data, setData, post, processing, errors } = useForm({
    email: "", // Changed from username
    password: "",
});

// Form field
<input
    id="email"
    name="email"
    type="email" // Changed from text
    required
    value={data.email}
    onChange={(e) => setData("email", e.target.value)}
    placeholder="Masukkan email"
/>;
```

### 4. ✅ Admin User Creation Updated

**File**: `create-admin-user.php`

**Changes**:

```php
$admin = Admin::create([
    'name' => 'Super Admin',        // New field
    'email' => 'admin@example.com', // New field
    'password' => Hash::make('password123')
]);
```

## 🧪 Testing

### 1. Test Admin Model Update

```bash
php test-admin-model-update.php
```

**Tests**:

-   Admin model configuration
-   Fillable fields verification
-   Guard configuration
-   User creation/update
-   Authentication testing
-   Form field verification
-   Controller validation

### 2. Create Admin User

```bash
php create-admin-user.php
```

**Creates admin with**:

-   Name: Super Admin
-   Email: admin@example.com
-   Password: password123

## 🚀 Deployment Steps

### 1. Upload Files to VPS

```bash
scp -r superadmin-backend/ user@103.23.198.101:/var/www/superadmin/
```

### 2. Create Admin User

```bash
cd /var/www/superadmin/superadmin-campusway
php create-admin-user.php
```

### 3. Clear All Caches

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 4. Rebuild Assets

```bash
npm run build
```

## 🔍 Verification

### 1. Test Login Form

-   ✅ Shows email field (not username)
-   ✅ Email input has correct type
-   ✅ Proper validation and error handling
-   ✅ No username references

### 2. Test Authentication

-   ✅ Login with email: `admin@example.com`
-   ✅ Login with password: `password123`
-   ✅ Redirects to correct dashboard
-   ✅ Proper error messages

### 3. Test Admin Model

-   ✅ Guard set to 'admin'
-   ✅ Fillable fields: name, email, password
-   ✅ Authentication works correctly
-   ✅ User data accessible

## 📊 Expected Results

### ✅ Login Form

-   Shows "Super Admin Login" title
-   Has email field (not username)
-   Has password field
-   Proper validation and error handling

### ✅ Authentication

-   Uses email/password combination
-   Proper guard isolation
-   Correct redirect after login
-   Proper error messages

### ✅ Admin Model

-   Guard: 'admin'
-   Fillable: name, email, password
-   Authentication works correctly
-   User data accessible

## 🔐 Login Credentials

-   **Email**: `admin@example.com`
-   **Password**: `password123`
-   **URL**: `http://103.23.198.101/super-admin`

## 🚨 Common Issues & Solutions

### Issue 1: Login fails

**Cause**: Admin user doesn't exist or wrong credentials
**Solution**:

1. Run `php create-admin-user.php`
2. Check database connection
3. Verify admin model configuration

### Issue 2: Form shows username field

**Cause**: Assets not rebuilt
**Solution**:

1. Run `npm run build`
2. Clear browser cache
3. Check if correct files are deployed

### Issue 3: Validation errors

**Cause**: Controller not updated
**Solution**:

1. Check controller validation rules
2. Clear Laravel caches
3. Verify form field names

## 🎉 Status: ADMIN MODEL UPDATED

The Admin model has been successfully updated:

-   ✅ Uses name, email, password fields
-   ✅ Guard set to 'admin'
-   ✅ Login form updated to use email
-   ✅ Controller updated for email validation
-   ✅ Admin user creation updated

---

**Last Updated**: $(date)
**Version**: 1.0.0
**Status**: ✅ Complete
