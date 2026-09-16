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
        Schema::create('drone_telemetries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drone_id')->constrained()->cascadeOnDelete();
            $table->uuid('message_id');
            $table->unsignedBigInteger('sequence');
            $table->string('status');
            $table->unsignedTinyInteger('battery_percentage');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('observed_at');
            $table->timestamps();

            $table->unique('message_id');
            $table->unique(['drone_id', 'sequence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drone_telemetries');
    }
};
