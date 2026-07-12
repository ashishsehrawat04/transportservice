<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * City routes no longer track distance, so leads/quotes can no longer
     * source a value for this column. Made nullable (not dropped) to avoid
     * losing historical figures already recorded.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE transport_leads MODIFY distance_km DECIMAL(10,2) NULL');
        DB::statement('ALTER TABLE transport_quotes MODIFY distance_km DECIMAL(10,2) NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('UPDATE transport_leads SET distance_km = 0 WHERE distance_km IS NULL');
        DB::statement('ALTER TABLE transport_leads MODIFY distance_km DECIMAL(10,2) NOT NULL');
        DB::statement('UPDATE transport_quotes SET distance_km = 0 WHERE distance_km IS NULL');
        DB::statement('ALTER TABLE transport_quotes MODIFY distance_km DECIMAL(10,2) NOT NULL DEFAULT 0');
    }
};
