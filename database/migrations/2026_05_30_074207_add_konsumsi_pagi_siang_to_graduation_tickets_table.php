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
            $table->timestamp('konsumsi_pagi_at')->nullable()->after('konsumsi_at');
            $table->timestamp('konsumsi_siang_at')->nullable()->after('konsumsi_pagi_at');
            $table->unsignedBigInteger('konsumsi_pagi_by')->nullable()->after('konsumsi_pagi_at');
            $table->unsignedBigInteger('konsumsi_siang_by')->nullable()->after('konsumsi_siang_at');
            
            $table->foreign('konsumsi_pagi_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('konsumsi_siang_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('graduation_tickets', function (Blueprint $table) {
            $table->dropForeign(['konsumsi_pagi_by']);
            $table->dropForeign(['konsumsi_siang_by']);
            $table->dropColumn(['konsumsi_pagi_at', 'konsumsi_siang_at', 'konsumsi_pagi_by', 'konsumsi_siang_by']);
        });
    }
};
