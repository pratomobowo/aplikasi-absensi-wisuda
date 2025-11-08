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
        Schema::create('buku_wisudas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('graduation_event_id')->constrained('graduation_events')->onDelete('cascade');
            $table->string('filename'); // Original filename
            $table->string('file_path'); // Storage path
            $table->bigInteger('file_size')->nullable(); // File size in bytes
            $table->string('mime_type')->default('application/pdf');
            $table->integer('download_count')->default(0); // Track downloads
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();

            // Indexes for faster queries
            $table->index('graduation_event_id');
            $table->index('uploaded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_wisudas');
    }
};
