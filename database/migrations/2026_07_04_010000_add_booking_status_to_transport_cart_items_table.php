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
            $table->foreignId('transport_lead_id')->nullable()->after('city_route_id')->constrained('transport_leads')->nullOnDelete();
            $table->string('booking_status')->nullable()->after('transport_lead_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_cart_items', function (Blueprint $table) {
            $table->dropForeign(['transport_lead_id']);
            $table->dropColumn(['transport_lead_id', 'booking_status']);
        });
    }
};
