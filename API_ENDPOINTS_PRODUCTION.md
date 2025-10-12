# API Endpoints Production - TKA Web System

## Base URL Production
```
http://103.23.198.101/super-admin/api
```

## 1. STUDENT WEB API (Public - No Authentication)
**Prefix:** `/api/web`

### Authentication
- **POST** `/api/web/register-student` - Registrasi siswa baru
- **POST** `/api/web/login` - Login siswa

### Schools & Majors
- **GET** `/api/web/schools` - Mendapatkan daftar sekolah
- **GET** `/api/web/majors` - Mendapatkan semua jurusan aktif
- **GET** `/api/web/majors/{id}` - Mendapatkan detail jurusan
- **GET** `/api/web/questions` - Mendapatkan daftar pertanyaan
- **GET** `/api/web/health` - Health check

### Student Major Selection
- **POST** `/api/web/choose-major` - Memilih jurusan (pertama kali)
  - Body: `{ "student_id": number, "major_id": number }`
  
- **POST** `/api/web/change-major` - Mengubah pilihan jurusan
  - Body: `{ "student_id": number, "major_id": number }`
  
- **GET** `/api/web/student-choice/{studentId}` - Mendapatkan pilihan jurusan siswa
- **GET** `/api/web/major-status/{studentId}` - Mengecek status pilihan jurusan
- **GET** `/api/web/student-profile/{studentId}` - Mendapatkan profil siswa

### TKA Schedules
- **GET** `/api/web/tka-schedules` - Mendapatkan semua jadwal TKA
  - Query params: `?school_id={id}` (optional)
  
- **GET** `/api/web/tka-schedules/upcoming` - Mendapatkan jadwal TKA mendatang
  - Query params: `?school_id={id}` (optional)

---

## 2. SCHOOL DASHBOARD API (Authenticated)
**Prefix:** `/api/school`

### Authentication
- **POST** `/api/school/login` - Login sekolah (NPSN & password)
  - Body: `{ "npsn": string, "password": string }`
  - Response: `{ "success": true, "token": string, "data": {...} }`
  
- **POST** `/api/school/logout` - Logout sekolah
  - Headers: `Authorization: Bearer {token}`
  
- **GET** `/api/school/profile` - Mendapatkan profil sekolah
  - Headers: `Authorization: Bearer {token}`

### Dashboard
- **GET** `/api/school/dashboard` - Mendapatkan data dashboard
  - Headers: `Authorization: Bearer {token}`
  - Response: statistik, grafik, data sekolah

### Students Management
- **GET** `/api/school/students` - Mendapatkan daftar siswa
- **GET** `/api/school/students/{id}` - Mendapatkan detail siswa
- **POST** `/api/school/students` - Menambah siswa baru
- **PUT** `/api/school/students/{id}` - Mengupdate data siswa
- **DELETE** `/api/school/students/{id}` - Menghapus siswa
- **GET** `/api/school/students-without-choice` - Siswa belum memilih jurusan
- **GET** `/api/school/export-students` - Export data siswa ke CSV

### Import Students
- **POST** `/api/school/import-students` - Import siswa dari file
  - Content-Type: `multipart/form-data`
  - Body: `file` (Excel/CSV)
  
- **GET** `/api/school/import-template` - Download template import
- **GET** `/api/school/import-rules` - Mendapatkan aturan import

### Statistics
- **GET** `/api/school/major-statistics` - Statistik pilihan jurusan per sekolah

### Classes
- **GET** `/api/school/classes` - Mendapatkan daftar kelas

### TKA Schedules
- **GET** `/api/school/tka-schedules` - Mendapatkan jadwal TKA untuk sekolah
- **GET** `/api/school/tka-schedules/upcoming` - Jadwal TKA mendatang

---

## 3. PUBLIC API (SuperAdmin Integration)
**Prefix:** `/api/public`

### Public Endpoints
- **GET** `/api/public/schools` - Mendapatkan daftar sekolah
- **GET** `/api/public/questions` - Mendapatkan daftar pertanyaan
- **GET** `/api/public/majors` - Mendapatkan daftar jurusan
- **GET** `/api/public/health` - Health check API

---

## 4. TKA SCHEDULES (Public)

### Public TKA Endpoints
- **GET** `/api/tka-schedules` - Mendapatkan semua jadwal TKA
- **GET** `/api/tka-schedules/upcoming` - Mendapatkan jadwal TKA mendatang

---

## Response Format

### Success Response
```json
{
  "success": true,
  "data": {...},
  "message": "Success message (optional)"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {...} // validation errors (optional)
}
```

---

## Frontend Configuration

### Environment Variables
```env
# Production
NEXT_PUBLIC_API_BASE_URL=http://103.23.198.101/super-admin/api/school
NEXT_PUBLIC_STUDENT_API_BASE_URL=http://103.23.198.101/super-admin/api/web
NEXT_PUBLIC_SUPERADMIN_API_URL=http://103.23.198.101/super-admin/api
```

### URL Mapping Frontend → Backend

#### Student Web (tka-frontend-siswa)
| Frontend Call | Backend Endpoint |
|--------------|------------------|
| `studentApiService.login()` | `POST /api/web/login` |
| `studentApiService.register()` | `POST /api/web/register-student` |
| `studentApiService.getSchools()` | `GET /api/web/schools` |
| `studentApiService.getMajors()` | `GET /api/web/majors` |
| `studentApiService.getMajorDetails(id)` | `GET /api/web/majors/{id}` |
| `studentApiService.chooseMajor()` | `POST /api/web/choose-major` |
| `studentApiService.changeMajor()` | `POST /api/web/change-major` |
| `studentApiService.getStudentChoice(id)` | `GET /api/web/student-choice/{id}` |
| `studentApiService.checkMajorStatus(id)` | `GET /api/web/major-status/{id}` |
| `studentApiService.getStudentProfile(id)` | `GET /api/web/student-profile/{id}` |
| `studentApiService.getTkaSchedules()` | `GET /api/web/tka-schedules` |
| `studentApiService.getUpcomingTkaSchedules()` | `GET /api/web/tka-schedules/upcoming` |

#### School Dashboard
| Frontend Call | Backend Endpoint |
|--------------|------------------|
| `apiService.login()` | `POST /api/school/login` |
| `apiService.logout()` | `POST /api/school/logout` |
| `apiService.getProfile()` | `GET /api/school/profile` |
| `apiService.getDashboard()` | `GET /api/school/dashboard` |
| `apiService.getStudents()` | `GET /api/school/students` |
| `apiService.exportStudents()` | `GET /api/school/export-students` |
| `apiService.getTkaSchedules()` | `GET /api/school/tka-schedules` |

---

## Testing Endpoints

### Test dengan cURL

```bash
# Test health check
curl http://103.23.198.101/super-admin/api/web/health

# Test get schools
curl http://103.23.198.101/super-admin/api/web/schools

# Test get majors
curl http://103.23.198.101/super-admin/api/web/majors

# Test student login
curl -X POST http://103.23.198.101/super-admin/api/web/login \
  -H "Content-Type: application/json" \
  -d '{"nisn":"1234567890","password":"password"}'

# Test school login
curl -X POST http://103.23.198.101/super-admin/api/school/login \
  -H "Content-Type: application/json" \
  -d '{"npsn":"12345678","password":"password"}'
```

### Test dengan Browser
```
http://103.23.198.101/super-admin/api/web/health
http://103.23.198.101/super-admin/api/web/schools
http://103.23.198.101/super-admin/api/web/majors
http://103.23.198.101/super-admin/api/public/health
```

---

## Notes

1. **CORS Configuration**: Pastikan CORS sudah dikonfigurasi untuk menerima request dari domain frontend
2. **Rate Limiting**: Pertimbangkan menambahkan rate limiting untuk endpoint public
3. **Authentication**: Endpoint `/api/school/*` menggunakan Laravel Sanctum untuk autentikasi
4. **Error Handling**: Semua endpoint mengembalikan format JSON yang konsisten
5. **Validation**: Request validation menggunakan Laravel validation rules
6. **Logging**: Semua error di-log untuk debugging

---

## Deployment Checklist

- [ ] Routes sudah ditambahkan di `routes/api.php`
- [ ] Controller methods sudah lengkap
- [ ] CORS configuration sudah benar
- [ ] Environment variables sudah di-set
- [ ] Database migrations sudah dijalankan
- [ ] Test semua endpoint dengan cURL/Postman
- [ ] Update frontend API configuration
- [ ] Clear cache Laravel: `php artisan cache:clear`
- [ ] Clear config cache: `php artisan config:clear`
- [ ] Restart web server

