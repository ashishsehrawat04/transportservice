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
        Schema::create('city_routes', function (Blueprint $table) {
           $table->id();
            $table->string('from_city');
            $table->string('to_city');
            $table->decimal('distance_km', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['from_city', 'to_city']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('city_routes');
    }
};
