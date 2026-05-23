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
        Schema::table('buku_wisudas', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('file_path');
            $table->string('sambutan_rektor')->nullable()->after('cover_image');
            $table->string('sambutan_wakil_rektor_1')->nullable()->after('sambutan_rektor');
            $table->string('sambutan_wakil_rektor_2')->nullable()->after('sambutan_wakil_rektor_1');
            $table->string('sambutan_wakil_rektor_3')->nullable()->after('sambutan_wakil_rektor_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku_wisudas', function (Blueprint $table) {
            $table->dropColumn([
                'cover_image',
                'sambutan_rektor',
                'sambutan_wakil_rektor_1',
                'sambutan_wakil_rektor_2',
                'sambutan_wakil_rektor_3',
            ]);
        });
    }
};
