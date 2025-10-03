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
        Schema::table('major_recommendations', function (Blueprint $table) {
            if (!Schema::hasColumn('major_recommendations', 'category')) {
                $table->string('category')->after('major_name')->default('Saintek')->comment('Kategori jurusan: Saintek, Soshum, atau Campuran');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('major_recommendations', function (Blueprint $table) {
            if (Schema::hasColumn('major_recommendations', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
