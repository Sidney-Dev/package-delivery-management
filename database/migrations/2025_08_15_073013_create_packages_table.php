<?php

use App\Models\Delivery;
use App\Models\City;
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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Delivery::class)->constrained()->nullOnDelete();
            $table->string('sku')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('dimensions')->nullable();
            $table->enum('status', ['in transit', 'delivered', 'returned', 'pending'])->default('pending');
            $table->string('return_reason')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('users', 'id');
            $table->foreignIdFor(City::class)->nullable()->constrained('cities');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
