<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('graduation_events', function (Blueprint $table) {
            $table->enum('status', ['upcoming', 'active', 'completed'])->default('upcoming')->after('is_active');
        });

        // Migrate existing data: if is_active = true, set status to 'active', else 'upcoming'
        DB::table('graduation_events')->where('is_active', true)->update(['status' => 'active']);
        DB::table('graduation_events')->where('is_active', false)->update(['status' => 'upcoming']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('graduation_events', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};