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
        Schema::table('schools', function (Blueprint $table) {
            // Tambahkan kolom jenjang sekolah
            if (!Schema::hasColumn('schools', 'school_level')) {
                $table->enum('school_level', ['SMA/MA', 'SMK/MAK'])->default('SMA/MA')->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            if (Schema::hasColumn('schools', 'school_level')) {
                $table->dropColumn('school_level');
            }
        });
    }
};