<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_service_prices', function (Blueprint $table) {
            if (! Schema::hasColumn('transport_service_prices', 'calculation_type')) {
                $table->string('calculation_type')->default('distance')->after('description');
            }
        });

        Schema::table('transport_leads', function (Blueprint $table) {
            if (! Schema::hasColumn('transport_leads', 'calculation_type')) {
                $table->string('calculation_type')->default('distance')->after('distance_km');
            }
        });

        Schema::table('transport_quotes', function (Blueprint $table) {
            if (! Schema::hasColumn('transport_quotes', 'calculation_type')) {
                $table->string('calculation_type')->default('distance')->after('distance_km');
            }
        });

        Schema::table('shipment_price_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('shipment_price_logs', 'calculation_type')) {
                $table->string('calculation_type')->nullable()->after('distance_km');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipment_price_logs', function (Blueprint $table) {
            if (Schema::hasColumn('shipment_price_logs', 'calculation_type')) {
                $table->dropColumn('calculation_type');
            }
        });

        Schema::table('transport_quotes', function (Blueprint $table) {
            if (Schema::hasColumn('transport_quotes', 'calculation_type')) {
                $table->dropColumn('calculation_type');
            }
        });

        Schema::table('transport_leads', function (Blueprint $table) {
            if (Schema::hasColumn('transport_leads', 'calculation_type')) {
                $table->dropColumn('calculation_type');
            }
        });

        Schema::table('transport_service_prices', function (Blueprint $table) {
            if (Schema::hasColumn('transport_service_prices', 'calculation_type')) {
                $table->dropColumn('calculation_type');
            }
        });
    }
};
