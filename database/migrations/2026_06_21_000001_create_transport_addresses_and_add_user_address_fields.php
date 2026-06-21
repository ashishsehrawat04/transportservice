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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'transport_pickup_address')) {
                $table->dropColumn('transport_pickup_address');
            }

            if (Schema::hasColumn('users', 'transport_delivery_address')) {
                $table->dropColumn('transport_delivery_address');
            }

            if (! Schema::hasColumn('users', 'address_line_1')) {
                $table->text('address_line_1')->nullable()->after('mobile');
            }

            if (! Schema::hasColumn('users', 'address_line_2')) {
                $table->text('address_line_2')->nullable()->after('address_line_1');
            }

            if (! Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address_line_2');
            }

            if (! Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable()->after('city');
            }

            if (! Schema::hasColumn('users', 'country')) {
                $table->string('country')->nullable()->default('India')->after('state');
            }
        });

        if (! Schema::hasTable('transport_addresses')) {
            Schema::create('transport_addresses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('item_id')->constrained('transport_cart_items')->onDelete('cascade');
                $table->text('pickup_address')->nullable();
                $table->text('delivery_address')->nullable();
                $table->integer('status')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_addresses');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'country',
            ]);
        });
    }
};
