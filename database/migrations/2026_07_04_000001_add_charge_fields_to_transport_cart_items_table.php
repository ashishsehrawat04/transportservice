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
            $table->string('charge_basis')->default('weight')->after('estimated_total');
            $table->decimal('charge_weight_kg', 10, 2)->default(0)->after('charge_basis');
            $table->decimal('volumetric_weight_kg', 10, 2)->default(0)->after('charge_weight_kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_cart_items', function (Blueprint $table) {
            $table->dropColumn(['charge_basis', 'charge_weight_kg', 'volumetric_weight_kg']);
        });
    }
};
