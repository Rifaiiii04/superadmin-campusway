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
        // Add indexes for frequently queried columns
        
        // Schools table indexes
        Schema::table('schools', function (Blueprint $table) {
            $table->index('npsn');
            $table->index('name');
            $table->index('created_at');
        });
        
        // Students table indexes
        Schema::table('students', function (Blueprint $table) {
            $table->index('nisn');
            $table->index('school_id');
            $table->index('kelas');
            $table->index('created_at');
            $table->index(['school_id', 'kelas']); // Composite index for school + class queries
        });
        
        // Questions table indexes
        Schema::table('questions', function (Blueprint $table) {
            $table->index('subject');
            $table->index('type');
            $table->index('created_at');
            $table->index(['subject', 'type']); // Composite index for subject + type queries
        });
        
        // Question options table indexes
        Schema::table('question_options', function (Blueprint $table) {
            $table->index('question_id');
            $table->index('is_correct');
            $table->index(['question_id', 'is_correct']); // Composite index for correct answers
        });
        
        // Student choices table indexes
        Schema::table('student_choices', function (Blueprint $table) {
            $table->index('student_id');
            $table->index('major_id');
            $table->index('created_at');
            $table->index(['student_id', 'major_id']); // Composite index for student + major queries
        });
        
        // Major recommendations table indexes
        Schema::table('major_recommendations', function (Blueprint $table) {
            $table->index('category');
            $table->index('is_active');
            $table->index(['category', 'is_active']); // Composite index for active majors by category
        });
        
        // Results table indexes
        Schema::table('results', function (Blueprint $table) {
            $table->index('student_id');
            $table->index('created_at');
            $table->index(['student_id', 'created_at']); // Composite index for student results by date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes
        Schema::table('schools', function (Blueprint $table) {
            $table->dropIndex(['npsn']);
            $table->dropIndex(['name']);
            $table->dropIndex(['created_at']);
        });
        
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['nisn']);
            $table->dropIndex(['school_id']);
            $table->dropIndex(['kelas']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['school_id', 'kelas']);
        });
        
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['subject']);
            $table->dropIndex(['type']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['subject', 'type']);
        });
        
        Schema::table('question_options', function (Blueprint $table) {
            $table->dropIndex(['question_id']);
            $table->dropIndex(['is_correct']);
            $table->dropIndex(['question_id', 'is_correct']);
        });
        
        Schema::table('student_choices', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['major_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['student_id', 'major_id']);
        });
        
        Schema::table('major_recommendations', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['category', 'is_active']);
        });
        
        Schema::table('results', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['student_id', 'created_at']);
        });
    }
};
