-- Script untuk mengisi data pilihan jurusan siswa
-- Jalankan script ini jika seeder Laravel tidak berfungsi

-- Hapus data lama jika ada
DELETE FROM student_choices;

-- Insert data pilihan jurusan untuk beberapa siswa
-- Pastikan student_id dan major_id sesuai dengan data yang ada di database

-- Siswa 1 memilih Teknik Informatika (major_id = 1)
INSERT INTO student_choices (student_id, major_id, created_at, updated_at) 
VALUES (1, 1, GETDATE(), GETDATE());

-- Siswa 2 memilih Teknik Sipil (major_id = 2)
INSERT INTO student_choices (student_id, major_id, created_at, updated_at) 
VALUES (2, 2, GETDATE(), GETDATE());

-- Siswa 3 memilih Teknik Elektro (major_id = 3)
INSERT INTO student_choices (student_id, major_id, created_at, updated_at) 
VALUES (3, 3, GETDATE(), GETDATE());

-- Siswa 4 memilih Teknik Mesin (major_id = 4)
INSERT INTO student_choices (student_id, major_id, created_at, updated_at) 
VALUES (4, 4, GETDATE(), GETDATE());

-- Siswa 5 memilih Teknik Kimia (major_id = 5)
INSERT INTO student_choices (student_id, major_id, created_at, updated_at) 
VALUES (5, 5, GETDATE(), GETDATE());

-- Cek hasil
SELECT 
    sc.id,
    s.name as student_name,
    s.nisn,
    mr.major_name,
    mr.category,
    sc.created_at
FROM student_choices sc
JOIN students s ON sc.student_id = s.id
JOIN major_recommendations mr ON sc.major_id = mr.id
ORDER BY sc.id;

-- Cek berapa siswa yang sudah memilih jurusan
SELECT 
    COUNT(*) as total_choices,
    COUNT(DISTINCT student_id) as students_with_choice
FROM student_choices;

-- Cek berapa siswa yang belum memilih jurusan
SELECT 
    COUNT(*) as students_without_choice
FROM students s
LEFT JOIN student_choices sc ON s.id = sc.student_id
WHERE sc.student_id IS NULL;
