<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buku_wisudas', function (Blueprint $table) {
            $table->json('initial_pages')->nullable()->after('sambutan_wakil_rektor_3');
        });
    }

    public function down(): void
    {
        Schema::table('buku_wisudas', function (Blueprint $table) {
            $table->dropColumn('initial_pages');
        });
    }
};
