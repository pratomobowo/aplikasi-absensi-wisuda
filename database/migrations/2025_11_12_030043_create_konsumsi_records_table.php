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
        Schema::create('konsumsi_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('graduation_ticket_id')
                ->unique()
                ->constrained('graduation_tickets')
                ->onDelete('cascade');
            $table->foreignId('scanned_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('scanned_at')->useCurrent();
            $table->timestamps();

            // Indexes for performance
            $table->index('graduation_ticket_id');
            $table->index('scanned_by');
            $table->index('scanned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsumsi_records');
    }
};
