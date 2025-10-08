-- Script untuk menambahkan kolom PUSMENDIK ke tabel tka_schedules
-- Jalankan script ini di MySQL

USE arahpotensi;

-- Tambahkan kolom PUSMENDIK ke tabel tka_schedules
ALTER TABLE tka_schedules 
ADD COLUMN gelombang VARCHAR(255) NULL AFTER created_by,
ADD COLUMN hari_pelaksanaan VARCHAR(255) NULL AFTER gelombang,
ADD COLUMN exam_venue VARCHAR(255) NULL AFTER hari_pelaksanaan,
ADD COLUMN exam_room VARCHAR(255) NULL AFTER exam_venue,
ADD COLUMN contact_person VARCHAR(255) NULL AFTER exam_room,
ADD COLUMN contact_phone VARCHAR(255) NULL AFTER contact_person,
ADD COLUMN requirements TEXT NULL AFTER contact_phone,
ADD COLUMN materials_needed TEXT NULL AFTER requirements;

-- Verifikasi kolom yang ditambahkan
DESCRIBE tka_schedules;
