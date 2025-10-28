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
            // Rename columns
            $table->renameColumn('nim', 'npm');
            $table->renameColumn('program_studi', 'prodi');
        });

        Schema::table('mahasiswa', function (Blueprint $table) {
            // Add new columns
            $table->decimal('ipk', 3, 2)->after('fakultas');
            $table->string('yudisium')->nullable()->after('ipk');
            
            // Add indexes
            $table->index('npm');
            $table->index('prodi');
            $table->index('fakultas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['npm']);
            $table->dropIndex(['prodi']);
            $table->dropIndex(['fakultas']);
            
            // Drop new columns
            $table->dropColumn(['ipk', 'yudisium']);
        });

        Schema::table('mahasiswa', function (Blueprint $table) {
            // Rename columns back
            $table->renameColumn('npm', 'nim');
            $table->renameColumn('prodi', 'program_studi');
        });
    }
};
