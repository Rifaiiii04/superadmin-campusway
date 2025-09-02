-- SQL Statements untuk update data parent_phone saja
-- (Kolom parent_phone sudah ada)

-- 1. Update data parent_phone untuk siswa yang belum memiliki
UPDATE students 
SET parent_phone = CASE 
    WHEN id % 4 = 0 THEN '0812' + RIGHT('00000000' + CAST(ABS(CHECKSUM(NEWID())) % 100000000 AS VARCHAR), 8)
    WHEN id % 4 = 1 THEN '0813' + RIGHT('00000000' + CAST(ABS(CHECKSUM(NEWID())) % 100000000 AS VARCHAR), 8)
    WHEN id % 4 = 2 THEN '0852' + RIGHT('00000000' + CAST(ABS(CHECKSUM(NEWID())) % 100000000 AS VARCHAR), 8)
    ELSE '0853' + RIGHT('00000000' + CAST(ABS(CHECKSUM(NEWID())) % 100000000 AS VARCHAR), 8)
END
WHERE parent_phone IS NULL OR parent_phone = '';

-- 2. Verifikasi hasil
SELECT COUNT(*) as 'Total Students' FROM students;
SELECT COUNT(*) as 'Students with Parent Phone' FROM students WHERE parent_phone IS NOT NULL AND parent_phone != '';

-- 3. Sample data
SELECT TOP 5 name, parent_phone FROM students WHERE parent_phone IS NOT NULL;
