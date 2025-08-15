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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('license_no')->nullable();
            $table->foreignId('vehicle_id')->nullable()->constrained();
            $table->string('status')->default('active');
            $table->unsignedInteger('current_load')->default(0);
            $table->timestamp('last_ping_at')->nullable();
            $table->foreignId('current_city_id')->nullable()->constrained('cities');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
