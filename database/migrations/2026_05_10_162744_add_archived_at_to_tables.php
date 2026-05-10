<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('graduation_tickets', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('konsumsi_at');
            $table->index('archived_at');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('scanned_at');
            $table->index('archived_at');
        });

        Schema::table('konsumsi_records', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('scanned_at');
            $table->index('archived_at');
        });
    }

    public function down(): void
    {
        Schema::table('graduation_tickets', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });

        Schema::table('konsumsi_records', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });
    }
};