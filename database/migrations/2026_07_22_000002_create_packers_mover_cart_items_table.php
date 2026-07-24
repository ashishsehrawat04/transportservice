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
        Schema::create('packers_mover_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('guest_id')->nullable()->index();
            $table->foreignId('packers_mover_id')->nullable()->constrained('packers_movers')->onDelete('restrict');
            $table->string('item_name');
            $table->string('item_type')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('length_cm', 8, 2)->nullable();
            $table->decimal('width_cm', 8, 2)->nullable();
            $table->decimal('height_cm', 8, 2);
            $table->decimal('weight_kg', 8, 2);
            $table->date('pickup_date');
            $table->decimal('distance_km', 8, 2)->default(1);
            $table->decimal('estimated_total', 10, 2)->default(0);
            $table->string('charge_basis')->default('weight');
            $table->decimal('charge_weight_kg', 10, 2)->default(0);
            $table->decimal('volumetric_weight_kg', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packers_mover_cart_items');
    }
};
