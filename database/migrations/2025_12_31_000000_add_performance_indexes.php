<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for frequently queried columns using try-catch for idempotency
        
        // Schools table indexes
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_schools_npsn ON schools (npsn)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_schools_name ON schools (name)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_schools_created_at ON schools (created_at)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        // Students table indexes
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_students_nisn ON students (nisn)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_students_school_id ON students (school_id)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_students_kelas ON students (kelas)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_students_created_at ON students (created_at)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_students_school_id_kelas ON students (school_id, kelas)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        // Questions table indexes
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_questions_subject ON questions (subject)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_questions_type ON questions (type)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_questions_created_at ON questions (created_at)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_questions_subject_type ON questions (subject, type)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        // Question options table indexes
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_question_options_question_id ON question_options (question_id)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_question_options_is_correct ON question_options (is_correct)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_question_options_question_id_is_correct ON question_options (question_id, is_correct)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        // Student choices table indexes
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_student_choices_student_id ON student_choices (student_id)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_student_choices_major_id ON student_choices (major_id)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_student_choices_created_at ON student_choices (created_at)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_student_choices_student_id_major_id ON student_choices (student_id, major_id)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        // Major recommendations table indexes
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_major_recommendations_category ON major_recommendations (category)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_major_recommendations_is_active ON major_recommendations (is_active)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_major_recommendations_category_is_active ON major_recommendations (category, is_active)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        // Results table indexes
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_results_student_id ON results (student_id)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_results_created_at ON results (created_at)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            DB::statement('CREATE NONCLUSTERED INDEX idx_results_student_id_created_at ON results (student_id, created_at)');
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes using DB::statement for idempotency
        try {
            DB::statement('DROP INDEX IF EXISTS idx_schools_npsn ON schools');
            DB::statement('DROP INDEX IF EXISTS idx_schools_name ON schools');
            DB::statement('DROP INDEX IF EXISTS idx_schools_created_at ON schools');
            
            DB::statement('DROP INDEX IF EXISTS idx_students_nisn ON students');
            DB::statement('DROP INDEX IF EXISTS idx_students_school_id ON students');
            DB::statement('DROP INDEX IF EXISTS idx_students_kelas ON students');
            DB::statement('DROP INDEX IF EXISTS idx_students_created_at ON students');
            DB::statement('DROP INDEX IF EXISTS idx_students_school_id_kelas ON students');
            
            DB::statement('DROP INDEX IF EXISTS idx_questions_subject ON questions');
            DB::statement('DROP INDEX IF EXISTS idx_questions_type ON questions');
            DB::statement('DROP INDEX IF EXISTS idx_questions_created_at ON questions');
            DB::statement('DROP INDEX IF EXISTS idx_questions_subject_type ON questions');
            
            DB::statement('DROP INDEX IF EXISTS idx_question_options_question_id ON question_options');
            DB::statement('DROP INDEX IF EXISTS idx_question_options_is_correct ON question_options');
            DB::statement('DROP INDEX IF EXISTS idx_question_options_question_id_is_correct ON question_options');
            
            DB::statement('DROP INDEX IF EXISTS idx_student_choices_student_id ON student_choices');
            DB::statement('DROP INDEX IF EXISTS idx_student_choices_major_id ON student_choices');
            DB::statement('DROP INDEX IF EXISTS idx_student_choices_created_at ON student_choices');
            DB::statement('DROP INDEX IF EXISTS idx_student_choices_student_id_major_id ON student_choices');
            
            DB::statement('DROP INDEX IF EXISTS idx_major_recommendations_category ON major_recommendations');
            DB::statement('DROP INDEX IF EXISTS idx_major_recommendations_is_active ON major_recommendations');
            DB::statement('DROP INDEX IF EXISTS idx_major_recommendations_category_is_active ON major_recommendations');
            
            DB::statement('DROP INDEX IF EXISTS idx_results_student_id ON results');
            DB::statement('DROP INDEX IF EXISTS idx_results_created_at ON results');
            DB::statement('DROP INDEX IF EXISTS idx_results_student_id_created_at ON results');
        } catch (\Exception $e) {
            // Ignore errors if indexes don't exist
        }
    }
};
