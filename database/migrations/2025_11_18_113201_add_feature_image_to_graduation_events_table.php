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
        Schema::table('graduation_events', function (Blueprint $table) {
            $table->string('feature_image')->nullable()->after('maps_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('graduation_events', function (Blueprint $table) {
            $table->dropColumn('feature_image');
        });
    }
};
