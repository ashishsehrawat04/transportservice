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
        Schema::create('transport_service_prices', function (Blueprint $table) {
            $table->id();
            $table->string('item_type')->unique();
            $table->string('description')->nullable();
            $table->decimal('base_price', 10, 2)->default(0.00);
            $table->decimal('weight_rate_per_kg', 8, 2)->default(0.00);
            $table->decimal('volume_rate_per_cft', 8, 2)->default(0.00);
            $table->decimal('distance_rate_per_km', 8, 2)->default(0.00);
            $table->decimal('multiplier', 5, 2)->default(1.00);
            $table->decimal('min_charge', 10, 2)->default(0.00);
            $table->decimal('max_charge', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_service_prices');
    }
};
