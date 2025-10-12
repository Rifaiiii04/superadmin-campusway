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
            // PUSMENDIK Essential Fields
            $table->string('gelombang')->nullable()->after('created_by');
            $table->string('hari_pelaksanaan')->nullable()->after('gelombang');
            $table->string('exam_venue')->nullable()->after('hari_pelaksanaan');
            $table->string('exam_room')->nullable()->after('exam_venue');
            $table->string('contact_person')->nullable()->after('exam_room');
            $table->string('contact_phone')->nullable()->after('contact_person');
            $table->text('requirements')->nullable()->after('contact_phone');
            $table->text('materials_needed')->nullable()->after('requirements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tka_schedules', function (Blueprint $table) {
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
