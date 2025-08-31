<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // File: database/migrations/2025_08_31_014748_create_schools_table.php

public function up(): void
{
    Schema::create('schools', function (Blueprint $table) {
        $table->id(); 
        $table->string('npsn', 8)->unique(); 
        $table->string('name'); 
        $table->string('password_hash'); 
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
