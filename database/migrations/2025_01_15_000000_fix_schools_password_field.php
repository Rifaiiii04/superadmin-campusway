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
        // Copy data from password_hash to password if password is null
        DB::statement('UPDATE schools SET password = password_hash WHERE password IS NULL');
        
        // Make password field NOT NULL
        Schema::table('schools', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
        });
        
        // Drop password_hash column
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('password_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back password_hash column
        Schema::table('schools', function (Blueprint $table) {
            $table->string('password_hash')->after('name');
        });
        
        // Copy data from password to password_hash
        DB::statement('UPDATE schools SET password_hash = password');
        
        // Make password nullable again
        Schema::table('schools', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });
    }
};
