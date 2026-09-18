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
        Schema::table('drones', function (Blueprint $table) {
            $table->timestamp('last_telemetry_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drones', function (Blueprint $table) {
            $table->dropColumn('last_telemetry_at');
        });
    }
};
