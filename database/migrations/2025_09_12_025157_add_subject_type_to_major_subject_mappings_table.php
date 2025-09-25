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
        Schema::table('major_subject_mappings', function (Blueprint $table) {
            if (!Schema::hasColumn('major_subject_mappings', 'subject_type')) {
                $table->string('subject_type')->nullable()->after('subject_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('major_subject_mappings', function (Blueprint $table) {
            if (Schema::hasColumn('major_subject_mappings', 'subject_type')) {
                $table->dropColumn('subject_type');
            }
        });
    }
};