-- Database Optimization Queries for TKA SuperAdmin Backend
-- Run these queries to optimize database performance

-- Create indexes for frequently queried columns
CREATE INDEX IF NOT EXISTS idx_students_school_id ON students(school_id);
CREATE INDEX IF NOT EXISTS idx_students_nisn ON students(nisn);
CREATE INDEX IF NOT EXISTS idx_students_email ON students(email);
CREATE INDEX IF NOT EXISTS idx_students_created_at ON students(created_at);

CREATE INDEX IF NOT EXISTS idx_schools_name ON schools(name);
CREATE INDEX IF NOT EXISTS idx_schools_province ON schools(province);
CREATE INDEX IF NOT EXISTS idx_schools_city ON schools(city);

CREATE INDEX IF NOT EXISTS idx_majors_category ON majors(category);
CREATE INDEX IF NOT EXISTS idx_majors_is_active ON majors(is_active);
CREATE INDEX IF NOT EXISTS idx_majors_name ON majors(name);

CREATE INDEX IF NOT EXISTS idx_tka_schedules_start_date ON tka_schedules(start_date);
CREATE INDEX IF NOT EXISTS idx_tka_schedules_end_date ON tka_schedules(end_date);
CREATE INDEX IF NOT EXISTS idx_tka_schedules_status ON tka_schedules(status);

CREATE INDEX IF NOT EXISTS idx_student_choices_student_id ON student_choices(student_id);
CREATE INDEX IF NOT EXISTS idx_student_choices_major_id ON student_choices(major_id);
CREATE INDEX IF NOT EXISTS idx_student_choices_created_at ON student_choices(created_at);

-- Create composite indexes for common query patterns
CREATE INDEX IF NOT EXISTS idx_students_school_active ON students(school_id, is_active);
CREATE INDEX IF NOT EXISTS idx_majors_category_active ON majors(category, is_active);
CREATE INDEX IF NOT EXISTS idx_tka_schedules_date_status ON tka_schedules(start_date, status);

-- Optimize table storage
OPTIMIZE TABLE students;
OPTIMIZE TABLE schools;
OPTIMIZE TABLE majors;
OPTIMIZE TABLE tka_schedules;
OPTIMIZE TABLE student_choices;

-- Update table statistics
ANALYZE TABLE students;
ANALYZE TABLE schools;
ANALYZE TABLE majors;
ANALYZE TABLE tka_schedules;
ANALYZE TABLE student_choices;

-- Create views for frequently accessed data
CREATE OR REPLACE VIEW v_schools_with_student_count AS
SELECT 
    s.*,
    COUNT(st.id) as student_count,
    COUNT(CASE WHEN st.is_active = 1 THEN 1 END) as active_student_count
FROM schools s
LEFT JOIN students st ON s.id = st.school_id
GROUP BY s.id;

CREATE OR REPLACE VIEW v_majors_with_choice_count AS
SELECT 
    m.*,
    COUNT(sc.id) as choice_count,
    COUNT(CASE WHEN sc.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as recent_choice_count
FROM majors m
LEFT JOIN student_choices sc ON m.id = sc.major_id
WHERE m.is_active = 1
GROUP BY m.id;

CREATE OR REPLACE VIEW v_upcoming_tka_schedules AS
SELECT 
    ts.*,
    s.name as school_name,
    s.province,
    s.city
FROM tka_schedules ts
LEFT JOIN schools s ON ts.school_id = s.id
WHERE ts.start_date >= NOW()
AND ts.status = 'active'
ORDER BY ts.start_date ASC;

-- Create stored procedures for common operations
DELIMITER //

CREATE PROCEDURE IF NOT EXISTS GetSchoolDashboard(IN school_id INT)
BEGIN
    SELECT 
        s.name as school_name,
        s.address,
        s.province,
        s.city,
        COUNT(st.id) as total_students,
        COUNT(CASE WHEN st.is_active = 1 THEN 1 END) as active_students,
        COUNT(sc.id) as total_choices,
        COUNT(CASE WHEN sc.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as recent_choices
    FROM schools s
    LEFT JOIN students st ON s.id = st.school_id
    LEFT JOIN student_choices sc ON st.id = sc.student_id
    WHERE s.id = school_id
    GROUP BY s.id;
END //

CREATE PROCEDURE IF NOT EXISTS GetStudentMajorChoices(IN student_id INT)
BEGIN
    SELECT 
        sc.*,
        m.name as major_name,
        m.category as major_category,
        m.description as major_description
    FROM student_choices sc
    JOIN majors m ON sc.major_id = m.id
    WHERE sc.student_id = student_id
    ORDER BY sc.created_at DESC;
END //

DELIMITER ;

-- Grant necessary permissions
GRANT EXECUTE ON PROCEDURE GetSchoolDashboard TO 'tka_user'@'%';
GRANT EXECUTE ON PROCEDURE GetStudentMajorChoices TO 'tka_user'@'%';

-- Flush privileges
FLUSH PRIVILEGES;
