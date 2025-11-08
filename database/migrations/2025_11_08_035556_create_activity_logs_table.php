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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // User information
            $table->string('user_type')->nullable(); // 'user' for admin, 'mahasiswa' for student
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable(); // Store user name for historical record
            $table->string('user_email')->nullable();

            // Action information
            $table->string('action'); // 'login', 'logout', 'view', 'create', 'update', 'delete', etc.
            $table->string('model')->nullable(); // Model name (e.g., 'Mahasiswa', 'User', 'Attendance')
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the affected record
            $table->string('model_name')->nullable(); // Human-readable name of the record

            // Change details
            $table->text('description')->nullable(); // Summary of the action
            $table->json('old_values')->nullable(); // Previous values for update operations
            $table->json('new_values')->nullable(); // New values for create/update operations

            // Request information
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable(); // GET, POST, PUT, DELETE

            $table->timestamps();

            // Indices for performance
            $table->index('user_id');
            $table->index('action');
            $table->index('model');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
