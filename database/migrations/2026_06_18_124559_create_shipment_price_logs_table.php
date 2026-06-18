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
        Schema::create('shipment_price_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('volume_cft', 12, 2)->nullable();
            $table->decimal('distance_km', 12, 2)->nullable();
            $table->decimal('base_price', 12, 2)->nullable();
            $table->decimal('weight_charge', 12, 2)->nullable();
            $table->decimal('volume_charge', 12, 2)->nullable();
            $table->decimal('distance_charge', 12, 2)->nullable();
            $table->decimal('multiplier_applied', 12, 2)->nullable();
            $table->decimal('subtotal', 12, 2)->nullable();
            $table->decimal('tax_amount', 12, 2)->nullable();
            $table->decimal('discount_amount', 12, 2)->nullable();
            $table->decimal('total_payment', 12, 2)->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_price_logs');
    }
};
