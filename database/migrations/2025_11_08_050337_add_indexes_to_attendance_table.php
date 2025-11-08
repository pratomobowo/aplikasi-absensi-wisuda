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
        Schema::table('attendances', function (Blueprint $table) {
            // Composite index for faster duplicate detection during scanning
            // This speeds up the query: WHERE graduation_ticket_id = ? AND role = ?
            $table->index(['graduation_ticket_id', 'role'], 'idx_attendance_ticket_role');

            // Index for finding attendance by ticket (common lookup)
            $table->index('graduation_ticket_id', 'idx_attendance_ticket');

            // Index for role-based queries
            $table->index('role', 'idx_attendance_role');

            // Index for scanned_at for analytics/reporting
            $table->index('scanned_at', 'idx_attendance_scanned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('idx_attendance_ticket_role');
            $table->dropIndex('idx_attendance_ticket');
            $table->dropIndex('idx_attendance_role');
            $table->dropIndex('idx_attendance_scanned_at');
        });
    }
};
