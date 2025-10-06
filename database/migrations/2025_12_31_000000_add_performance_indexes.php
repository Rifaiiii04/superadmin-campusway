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
            Schema::table('schools', function (Blueprint $table) {
                $table->index('npsn');
                $table->index('name');
                $table->index('created_at');
            });
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        // Students table indexes
        try {
            Schema::table('students', function (Blueprint $table) {
                $table->index('nisn');
                $table->index('school_id');
                $table->index('kelas');
                $table->index('created_at');
                $table->index(['school_id', 'kelas']); // Composite index for school + class queries
            });
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        // Questions table indexes
        try {
            Schema::table('questions', function (Blueprint $table) {
                $table->index('subject');
                $table->index('type');
                $table->index('created_at');
                $table->index(['subject', 'type']); // Composite index for subject + type queries
            });
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        // Question options table indexes
        try {
            Schema::table('question_options', function (Blueprint $table) {
                $table->index('question_id');
                $table->index('is_correct');
                $table->index(['question_id', 'is_correct']); // Composite index for correct answers
            });
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        // Student choices table indexes
        try {
            Schema::table('student_choices', function (Blueprint $table) {
                $table->index('student_id');
                $table->index('major_id');
                $table->index('created_at');
                $table->index(['student_id', 'major_id']); // Composite index for student + major queries
            });
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        // Major recommendations table indexes
        try {
            Schema::table('major_recommendations', function (Blueprint $table) {
                $table->index('category');
                $table->index('is_active');
                $table->index(['category', 'is_active']); // Composite index for active majors by category
            });
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        // Results table indexes
        try {
            Schema::table('results', function (Blueprint $table) {
                $table->index('student_id');
                $table->index('created_at');
                $table->index(['student_id', 'created_at']); // Composite index for student results by date
            });
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes using try-catch for safe rollback
        try {
            Schema::table('schools', function (Blueprint $table) {
                $table->dropIndex(['npsn']);
                $table->dropIndex(['name']);
                $table->dropIndex(['created_at']);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, ignore
        }
        
        try {
            Schema::table('students', function (Blueprint $table) {
                $table->dropIndex(['nisn']);
                $table->dropIndex(['school_id']);
                $table->dropIndex(['kelas']);
                $table->dropIndex(['created_at']);
                $table->dropIndex(['school_id', 'kelas']);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, ignore
        }
        
        try {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropIndex(['subject']);
                $table->dropIndex(['type']);
                $table->dropIndex(['created_at']);
                $table->dropIndex(['subject', 'type']);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, ignore
        }
        
        try {
            Schema::table('question_options', function (Blueprint $table) {
                $table->dropIndex(['question_id']);
                $table->dropIndex(['is_correct']);
                $table->dropIndex(['question_id', 'is_correct']);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, ignore
        }
        
        try {
            Schema::table('student_choices', function (Blueprint $table) {
                $table->dropIndex(['student_id']);
                $table->dropIndex(['major_id']);
                $table->dropIndex(['created_at']);
                $table->dropIndex(['student_id', 'major_id']);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, ignore
        }
        
        try {
            Schema::table('major_recommendations', function (Blueprint $table) {
                $table->dropIndex(['category']);
                $table->dropIndex(['is_active']);
                $table->dropIndex(['category', 'is_active']);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, ignore
        }
        
        try {
            Schema::table('results', function (Blueprint $table) {
                $table->dropIndex(['student_id']);
                $table->dropIndex(['created_at']);
                $table->dropIndex(['student_id', 'created_at']);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, ignore
        }
    }
};
