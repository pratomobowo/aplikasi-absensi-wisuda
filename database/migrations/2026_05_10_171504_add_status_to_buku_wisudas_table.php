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
        Schema::table('buku_wisudas', function (Blueprint $table) {
            $table->enum('status', ['draft', 'generated', 'published'])->default('draft')->after('graduation_event_id');
            $table->timestamp('generated_at')->nullable()->after('uploaded_at');
            $table->text('generated_by')->nullable()->after('generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku_wisudas', function (Blueprint $table) {
            $table->dropColumn(['status', 'generated_at', 'generated_by']);
        });
    }
};