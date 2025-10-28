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
        // Check if nim column exists and rename to npm
        if (Schema::hasColumn('mahasiswa', 'nim')) {
            Schema::table('mahasiswa', function (Blueprint $table) {
                $table->renameColumn('nim', 'npm');
            });
        }

        Schema::table('mahasiswa', function (Blueprint $table) {
            // Add ipk column if it doesn't exist
            if (!Schema::hasColumn('mahasiswa', 'ipk')) {
                $table->decimal('ipk', 3, 2)->default(0)->after('fakultas');
            }
            
            // Add yudisium column if it doesn't exist
            if (!Schema::hasColumn('mahasiswa', 'yudisium')) {
                $table->string('yudisium')->nullable()->after('ipk');
            }
        });

        Schema::table('mahasiswa', function (Blueprint $table) {
            // Add indexes if they don't exist
            $indexes = Schema::getIndexes('mahasiswa');
            $indexNames = array_column($indexes, 'name');
            
            if (!in_array('mahasiswa_npm_index', $indexNames)) {
                $table->index('npm');
            }
            if (!in_array('mahasiswa_program_studi_index', $indexNames)) {
                $table->index('program_studi');
            }
            if (!in_array('mahasiswa_fakultas_index', $indexNames)) {
                $table->index('fakultas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            // Drop indexes if they exist
            $indexes = Schema::getIndexes('mahasiswa');
            $indexNames = array_column($indexes, 'name');
            
            if (in_array('mahasiswa_npm_index', $indexNames)) {
                $table->dropIndex(['npm']);
            }
            if (in_array('mahasiswa_program_studi_index', $indexNames)) {
                $table->dropIndex(['program_studi']);
            }
            if (in_array('mahasiswa_fakultas_index', $indexNames)) {
                $table->dropIndex(['fakultas']);
            }
        });

        Schema::table('mahasiswa', function (Blueprint $table) {
            // Drop new columns if they exist
            if (Schema::hasColumn('mahasiswa', 'ipk')) {
                $table->dropColumn('ipk');
            }
            if (Schema::hasColumn('mahasiswa', 'yudisium')) {
                $table->dropColumn('yudisium');
            }
        });

        Schema::table('mahasiswa', function (Blueprint $table) {
            // Rename npm back to nim
            if (Schema::hasColumn('mahasiswa', 'npm')) {
                $table->renameColumn('npm', 'nim');
            }
        });
    }
};
