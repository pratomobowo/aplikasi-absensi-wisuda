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
        Schema::table('mahasiswa', function (Blueprint $table) {
            // Change IPK column from decimal(3,2) to decimal(4,2) to support values like 3.89
            // This allows values from 0.00 to 99.99 with 2 decimal places
            $table->decimal('ipk', 4, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            // Revert to original decimal(3,2)
            $table->decimal('ipk', 3, 2)->change();
        });
    }
};
