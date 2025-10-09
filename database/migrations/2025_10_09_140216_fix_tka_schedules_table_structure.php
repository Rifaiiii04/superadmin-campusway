<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tka_schedules', function (Blueprint $table) {
            // Fix description column to be TEXT instead of VARCHAR(0)
            $table->text('description')->nullable()->change();
            
            // Fix instructions column to be TEXT instead of VARCHAR(0)
            $table->text('instructions')->nullable()->change();
            
            // Fix target_schools column to be JSON instead of VARCHAR(0)
            $table->json('target_schools')->nullable()->change();
            
            // Add missing PUSMENDIK fields if they don't exist
            if (!Schema::hasColumn('tka_schedules', 'gelombang')) {
                $table->string('gelombang')->nullable();
            }
            if (!Schema::hasColumn('tka_schedules', 'hari_pelaksanaan')) {
                $table->string('hari_pelaksanaan')->nullable();
            }
            if (!Schema::hasColumn('tka_schedules', 'exam_venue')) {
                $table->string('exam_venue')->nullable();
            }
            if (!Schema::hasColumn('tka_schedules', 'exam_room')) {
                $table->string('exam_room')->nullable();
            }
            if (!Schema::hasColumn('tka_schedules', 'contact_person')) {
                $table->string('contact_person')->nullable();
            }
            if (!Schema::hasColumn('tka_schedules', 'contact_phone')) {
                $table->string('contact_phone')->nullable();
            }
            if (!Schema::hasColumn('tka_schedules', 'requirements')) {
                $table->text('requirements')->nullable();
            }
            if (!Schema::hasColumn('tka_schedules', 'materials_needed')) {
                $table->text('materials_needed')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tka_schedules', function (Blueprint $table) {
            // Revert description column
            $table->string('description')->nullable()->change();
            
            // Revert instructions column
            $table->string('instructions')->nullable()->change();
            
            // Revert target_schools column
            $table->string('target_schools')->nullable()->change();
            
            // Remove PUSMENDIK fields
            $table->dropColumn([
                'gelombang',
                'hari_pelaksanaan',
                'exam_venue',
                'exam_room',
                'contact_person',
                'contact_phone',
                'requirements',
                'materials_needed'
            ]);
        });
    }
};