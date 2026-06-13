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
        Schema::table('transport_cart_items', function (Blueprint $table) {
            $table->foreignId('city_route_id')->nullable()->after('user_id')->constrained('city_routes')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_cart_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_route_id');
        });
    }
};
