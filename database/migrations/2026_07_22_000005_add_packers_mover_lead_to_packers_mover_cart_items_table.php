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
        Schema::table('packers_mover_cart_items', function (Blueprint $table) {
            $table->foreignId('packers_mover_lead_id')->nullable()->after('packers_mover_id')->constrained('packers_mover_leads')->nullOnDelete();
            $table->string('booking_status')->nullable()->after('packers_mover_lead_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packers_mover_cart_items', function (Blueprint $table) {
            $table->dropForeign(['packers_mover_lead_id']);
            $table->dropColumn(['packers_mover_lead_id', 'booking_status']);
        });
    }
};
