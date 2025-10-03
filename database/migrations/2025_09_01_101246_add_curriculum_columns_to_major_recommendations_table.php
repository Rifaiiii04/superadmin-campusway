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
            if (!Schema::hasColumn('major_recommendations', 'kurikulum_merdeka_subjects')) {
                $table->json('kurikulum_merdeka_subjects')->nullable()->after('preferred_subjects');
            }
            if (!Schema::hasColumn('major_recommendations', 'kurikulum_2013_ipa_subjects')) {
                $table->json('kurikulum_2013_ipa_subjects')->nullable()->after('kurikulum_merdeka_subjects');
            }
            if (!Schema::hasColumn('major_recommendations', 'kurikulum_2013_ips_subjects')) {
                $table->json('kurikulum_2013_ips_subjects')->nullable()->after('kurikulum_2013_ipa_subjects');
            }
            if (!Schema::hasColumn('major_recommendations', 'kurikulum_2013_bahasa_subjects')) {
                $table->json('kurikulum_2013_bahasa_subjects')->nullable()->after('kurikulum_2013_ips_subjects');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('major_recommendations', function (Blueprint $table) {
            if (Schema::hasColumn('major_recommendations', 'kurikulum_merdeka_subjects')) {
                $table->dropColumn('kurikulum_merdeka_subjects');
            }
            if (Schema::hasColumn('major_recommendations', 'kurikulum_2013_ipa_subjects')) {
                $table->dropColumn('kurikulum_2013_ipa_subjects');
            }
            if (Schema::hasColumn('major_recommendations', 'kurikulum_2013_ips_subjects')) {
                $table->dropColumn('kurikulum_2013_ips_subjects');
            }
            if (Schema::hasColumn('major_recommendations', 'kurikulum_2013_bahasa_subjects')) {
                $table->dropColumn('kurikulum_2013_bahasa_subjects');
            }
        });
    }
};
