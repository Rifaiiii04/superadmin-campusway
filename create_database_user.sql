-- Script untuk membuat user campusway_admin di SQL Server
-- Jalankan script ini di SQL Server Management Studio (SSMS)

-- 1. Buat login untuk campusway_admin
CREATE LOGIN campusway_admin WITH PASSWORD = 'P@ssw0rdBaru!2025';

-- 2. Pastikan database campusway_db ada (jika belum ada, buat dulu)
IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = 'campusway_db')
BEGIN
    CREATE DATABASE campusway_db;
END

-- 3. Gunakan database campusway_db
USE campusway_db;

-- 4. Buat user campusway_admin di database campusway_db
CREATE USER campusway_admin FOR LOGIN campusway_admin;

-- 5. Berikan permission db_owner ke user campusway_admin
ALTER ROLE db_owner ADD MEMBER campusway_admin;

-- 6. Berikan permission tambahan yang mungkin diperlukan
GRANT CREATE TABLE TO campusway_admin;
GRANT CREATE PROCEDURE TO campusway_admin;
GRANT CREATE VIEW TO campusway_admin;
GRANT CREATE FUNCTION TO campusway_admin;

-- 7. Cek apakah user berhasil dibuat
SELECT 
    name as 'Database User',
    type_desc as 'User Type',
    create_date as 'Created Date'
FROM sys.database_principals 
WHERE name = 'campusway_admin';

-- 8. Cek login yang dibuat
SELECT 
    name as 'Login Name',
    type_desc as 'Login Type',
    create_date as 'Created Date',
    is_disabled as 'Is Disabled'
FROM sys.server_principals 
WHERE name = 'campusway_admin';

PRINT 'User campusway_admin berhasil dibuat dengan password P@ssw0rdBaru!2025';
PRINT 'User memiliki permission db_owner di database campusway_db';
