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
        Schema::table('graduation_tickets', function (Blueprint $table) {
            $table->boolean('konsumsi_diterima')->default(false)->after('is_distributed');
            $table->timestamp('konsumsi_at')->nullable()->after('konsumsi_diterima');
            $table->index('konsumsi_diterima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('graduation_tickets', function (Blueprint $table) {
            $table->dropIndex(['konsumsi_diterima']);
            $table->dropColumn(['konsumsi_diterima', 'konsumsi_at']);
        });
    }
};
