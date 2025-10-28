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
        // Add indexes to mahasiswa table for filtering and searching
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->index('program_studi');
            $table->index('fakultas');
            $table->index(['program_studi', 'fakultas']);
        });

        // Add indexes to graduation_tickets table for common queries
        Schema::table('graduation_tickets', function (Blueprint $table) {
            $table->index('mahasiswa_id');
            $table->index('graduation_event_id');
            $table->index('is_distributed');
            $table->index('expires_at');
            $table->index(['graduation_event_id', 'is_distributed']);
        });

        // Add indexes to graduation_events table for filtering
        Schema::table('graduation_events', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('date');
            $table->index(['is_active', 'date']);
        });

        // Add indexes to attendances table for reporting and filtering
        Schema::table('attendances', function (Blueprint $table) {
            $table->index('scanned_by');
            $table->index('scanned_at');
            $table->index('role');
            $table->index(['graduation_ticket_id', 'scanned_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes from mahasiswa table
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropIndex(['program_studi']);
            $table->dropIndex(['fakultas']);
            $table->dropIndex(['program_studi', 'fakultas']);
        });

        // Drop indexes from graduation_tickets table
        Schema::table('graduation_tickets', function (Blueprint $table) {
            $table->dropIndex(['mahasiswa_id']);
            $table->dropIndex(['graduation_event_id']);
            $table->dropIndex(['is_distributed']);
            $table->dropIndex(['expires_at']);
            $table->dropIndex(['graduation_event_id', 'is_distributed']);
        });

        // Drop indexes from graduation_events table
        Schema::table('graduation_events', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['date']);
            $table->dropIndex(['is_active', 'date']);
        });

        // Drop indexes from attendances table
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['scanned_by']);
            $table->dropIndex(['scanned_at']);
            $table->dropIndex(['role']);
            $table->dropIndex(['graduation_ticket_id', 'scanned_at']);
        });
    }
};
