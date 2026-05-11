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
            $table->text('qr_token_pendamping1')->nullable()->change();
            $table->text('qr_token_pendamping2')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('graduation_tickets', function (Blueprint $table) {
            $table->text('qr_token_pendamping1')->nullable(false)->change();
            $table->text('qr_token_pendamping2')->nullable(false)->change();
        });
    }
};
