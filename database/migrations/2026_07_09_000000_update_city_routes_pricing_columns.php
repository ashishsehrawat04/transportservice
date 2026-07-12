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
        Schema::table('city_routes', function (Blueprint $table) {
            $table->renameColumn('base_rate_per_weight', 'rate_per_weight');
        });

        Schema::table('city_routes', function (Blueprint $table) {
            $table->unsignedTinyInteger('transit_days')->default(1)->after('rate_per_weight');
        });

        Schema::table('city_routes', function (Blueprint $table) {
            $table->dropColumn(['distance_km', 'base_rate_per_volume']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('city_routes', function (Blueprint $table) {
            $table->decimal('distance_km', 8, 2)->default(0)->after('to_city');
            $table->integer('base_rate_per_volume')->default(0)->after('rate_per_weight');
        });

        Schema::table('city_routes', function (Blueprint $table) {
            $table->dropColumn('transit_days');
        });

        Schema::table('city_routes', function (Blueprint $table) {
            $table->renameColumn('rate_per_weight', 'base_rate_per_weight');
        });
    }
};
