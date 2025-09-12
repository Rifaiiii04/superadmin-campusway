@echo off
echo ========================================
echo    MEMPERBAIKI SISTEM MATA PELAJARAN
echo ========================================
echo.

echo [1/3] Memperbaiki mata pelajaran wajib...
php artisan db:seed --class=FixMandatorySubjectsSeeder
echo.

echo [2/3] Membuat mapping prodi ke mata pelajaran...
php artisan db:seed --class=CreateMajorSubjectMappingSeeder
echo.

echo [3/3] Menjalankan migrasi untuk tabel mapping...
php artisan migrate
echo.

echo ========================================
echo    SISTEM MATA PELAJARAN DIPERBAIKI
echo ========================================
echo.
echo ✅ Mata pelajaran wajib: Bahasa Indonesia, Bahasa Inggris, Matematika
echo ✅ Aturan SMA: 3 wajib + 2 pilihan sesuai prodi
echo ✅ Aturan SMK: 3 wajib + Produk/PKK + 1 pilihan sesuai prodi
echo ✅ Mapping prodi ke mata pelajaran: Selesai
echo.
echo Silakan restart server dan test sistem!
pause
