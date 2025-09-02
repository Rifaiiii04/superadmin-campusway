-- SQL Statements untuk menambahkan kolom parent_phone dan data

-- 1. Cek apakah kolom parent_phone sudah ada
IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
           WHERE TABLE_NAME = 'students' AND COLUMN_NAME = 'parent_phone')
BEGIN
    PRINT 'Kolom parent_phone sudah ada di table students';
END
ELSE
BEGIN
    ALTER TABLE students 
    ADD parent_phone NVARCHAR(20) NULL;
    PRINT 'Kolom parent_phone berhasil ditambahkan ke table students';
END

-- 2. Update data parent_phone untuk siswa yang belum memiliki
UPDATE students 
SET parent_phone = CASE 
    WHEN id % 4 = 0 THEN '0812' + RIGHT('00000000' + CAST(ABS(CHECKSUM(NEWID())) % 100000000 AS VARCHAR), 8)
    WHEN id % 4 = 1 THEN '0813' + RIGHT('00000000' + CAST(ABS(CHECKSUM(NEWID())) % 100000000 AS VARCHAR), 8)
    WHEN id % 4 = 2 THEN '0852' + RIGHT('00000000' + CAST(ABS(CHECKSUM(NEWID())) % 100000000 AS VARCHAR), 8)
    ELSE '0853' + RIGHT('00000000' + CAST(ABS(CHECKSUM(NEWID())) % 100000000 AS VARCHAR), 8)
END
WHERE parent_phone IS NULL OR parent_phone = '';

-- 3. Verifikasi hasil
SELECT COUNT(*) as 'Total Students' FROM students;
SELECT COUNT(*) as 'Students with Parent Phone' FROM students WHERE parent_phone IS NOT NULL AND parent_phone != '';

-- 4. Sample data
SELECT TOP 5 name, parent_phone FROM students WHERE parent_phone IS NOT NULL;
