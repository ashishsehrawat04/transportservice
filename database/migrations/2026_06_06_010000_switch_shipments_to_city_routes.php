<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transport_cart_items') && !Schema::hasColumn('transport_cart_items', 'city_route_id')) {
            Schema::table('transport_cart_items', function (Blueprint $table) {
                $table->foreignId('city_route_id')->nullable()->after('user_id')->constrained('city_routes')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('transport_leads') && !Schema::hasColumn('transport_leads', 'city_route_id')) {
            Schema::table('transport_leads', function (Blueprint $table) {
                $table->foreignId('city_route_id')->nullable()->after('volume_cft')->constrained('city_routes')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('cities')) {
            $this->mapExistingCityIdsToRoutes('transport_cart_items');
            $this->mapExistingCityIdsToRoutes('transport_leads');
        }

        $this->dropCityIdColumns('transport_cart_items');
        $this->dropCityIdColumns('transport_leads');
    }

    public function down(): void
    {
        if (Schema::hasTable('transport_cart_items') && Schema::hasColumn('transport_cart_items', 'city_route_id')) {
            Schema::table('transport_cart_items', function (Blueprint $table) {
                $table->dropConstrainedForeignId('city_route_id');
            });
        }

        if (Schema::hasTable('transport_leads') && Schema::hasColumn('transport_leads', 'city_route_id')) {
            Schema::table('transport_leads', function (Blueprint $table) {
                $table->dropConstrainedForeignId('city_route_id');
            });
        }
    }

    private function mapExistingCityIdsToRoutes(string $table): void
    {
        if (
            !Schema::hasTable($table)
            || !Schema::hasColumn($table, 'city_route_id')
            || !Schema::hasColumn($table, 'from_city_id')
            || !Schema::hasColumn($table, 'to_city_id')
        ) {
            return;
        }

        DB::statement("
            UPDATE {$table} target
            JOIN cities from_city ON from_city.id = target.from_city_id
            JOIN cities to_city ON to_city.id = target.to_city_id
            JOIN city_routes route ON (
                (route.from_city = from_city.name AND route.to_city = to_city.name)
                OR (route.from_city = to_city.name AND route.to_city = from_city.name)
            )
            SET target.city_route_id = route.id
            WHERE target.city_route_id IS NULL
        ");
    }

    private function dropCityIdColumns(string $table): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        foreach (['from_city_id', 'to_city_id'] as $column) {
            if (!Schema::hasColumn($table, $column)) {
                continue;
            }

            try {
                DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$table}_{$column}_foreign");
            } catch (\Throwable) {
                //
            }
            Schema::table($table, function (Blueprint $table) use ($column) {
                $table->dropColumn($column);
            });
        }
    }
};
