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
        Schema::create('packers_movers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->text('address')->nullable();
            $table->decimal('price_per_km_per_kg', 10, 2);
            $table->decimal('min_charge', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['name', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packers_movers');
    }
};
