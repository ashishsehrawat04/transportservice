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
            $table->foreignId('from_city_id')->nullable()->after('user_id')->constrained('cities')->onDelete('restrict');
            $table->foreignId('to_city_id')->nullable()->after('from_city_id')->constrained('cities')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_cart_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('from_city_id');
            $table->dropConstrainedForeignId('to_city_id');
        });
    }
};
