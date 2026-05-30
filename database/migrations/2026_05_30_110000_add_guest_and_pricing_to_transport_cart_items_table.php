<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transport_cart_items', function (Blueprint $table) {
            $table->string('guest_id')->nullable()->after('user_id')->index();
            $table->decimal('estimated_total', 10, 2)->default(0)->after('delivery_date');
        });

        DB::statement('ALTER TABLE transport_cart_items MODIFY user_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE transport_cart_items MODIFY user_id BIGINT UNSIGNED NOT NULL');

        Schema::table('transport_cart_items', function (Blueprint $table) {
            $table->dropColumn(['guest_id', 'estimated_total']);
        });
    }
};
