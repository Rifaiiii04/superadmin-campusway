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
            $table->decimal('min_score', 5, 2)->default(70.00)->after('description');
            $table->decimal('max_score', 5, 2)->default(100.00)->after('min_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('major_recommendations', function (Blueprint $table) {
            $table->dropColumn(['min_score', 'max_score']);
        });
    }
};
