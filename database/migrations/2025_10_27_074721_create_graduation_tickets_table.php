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
        Schema::create('graduation_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('graduation_event_id')->constrained('graduation_events')->onDelete('cascade');
            $table->string('magic_link_token')->unique();
            $table->text('qr_token_mahasiswa');
            $table->text('qr_token_pendamping1');
            $table->text('qr_token_pendamping2');
            $table->boolean('is_distributed')->default(false);
            $table->timestamp('distributed_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('graduation_tickets');
    }
};
