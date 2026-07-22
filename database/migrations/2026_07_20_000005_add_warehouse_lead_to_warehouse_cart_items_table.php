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
        Schema::table('warehouse_cart_items', function (Blueprint $table) {
            $table->foreignId('warehouse_lead_id')->nullable()->after('warehouse_id')->constrained('warehouse_leads')->nullOnDelete();
            $table->string('booking_status')->nullable()->after('warehouse_lead_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_cart_items', function (Blueprint $table) {
            $table->dropForeign(['warehouse_lead_id']);
            $table->dropColumn(['warehouse_lead_id', 'booking_status']);
        });
    }
};
