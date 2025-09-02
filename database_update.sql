-- SQL Statements untuk Update Database
-- Jalankan satu per satu sesuai urutan

-- 1. Tambahkan kolom category ke table major_recommendations
ALTER TABLE major_recommendations 
ADD category NVARCHAR(50) NOT NULL DEFAULT 'Saintek';

-- 2. Tambahkan kolom parent_phone ke table students  
ALTER TABLE students 
ADD parent_phone NVARCHAR(20) NULL;

-- 3. Update beberapa data major_recommendations dengan kategori yang sesuai
-- (Jalankan setelah kolom category sudah ditambahkan)
UPDATE major_recommendations 
SET category = 'Saintek' 
WHERE major_name LIKE '%Teknik%' OR major_name LIKE '%Informatika%' OR major_name LIKE '%Kedokteran%' OR major_name LIKE '%Sipil%';

UPDATE major_recommendations 
SET category = 'Soshum' 
WHERE major_name LIKE '%Akuntansi%' OR major_name LIKE '%Manajemen%' OR major_name LIKE '%Ekonomi%' OR major_name LIKE '%Sosiologi%';

-- 4. Update required_subjects untuk semua jurusan agar konsisten
UPDATE major_recommendations 
SET required_subjects = '["Matematika", "Bahasa Indonesia", "Bahasa Inggris"]';

-- 5. Verifikasi perubahan
SELECT major_name, category, required_subjects FROM major_recommendations LIMIT 5;
SELECT name, parent_phone FROM students LIMIT 5;
