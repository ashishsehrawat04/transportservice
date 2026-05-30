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
        Schema::create('transport_leads', function (Blueprint $table) {
           $table->id();

            // ---------- USER ----------
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // ---------- ITEM DETAILS ----------
            $table->string('item_name');
            $table->string('item_type');            // furniture, electronics etc (transport_service_prices se match karega)
            $table->integer('quantity')->default(1);

            // Dimensions (cm mein)
            $table->decimal('length_cm', 8, 2)->nullable();
            $table->decimal('width_cm', 8, 2)->nullable();
            $table->decimal('height_cm', 8, 2)->nullable();
            $table->decimal('weight_kg', 8, 2);
            $table->decimal('volume_cft', 8, 2)->nullable(); // auto calculate hoga

            // ---------- ROUTE ----------
            $table->foreignId('from_city_id')->constrained('cities')->onDelete('restrict');
            $table->foreignId('to_city_id')->constrained('cities')->onDelete('restrict');
            $table->decimal('distance_km', 10, 2);          // city route table se aayega

            // ---------- PRICE BREAKDOWN ----------
            $table->decimal('base_price', 10, 2)->default(0.00);         // base charge
            $table->decimal('weight_charge', 10, 2)->default(0.00);      // weight × rate_per_kg
            $table->decimal('volume_charge', 10, 2)->default(0.00);      // volume × rate_per_cft
            $table->decimal('distance_charge', 10, 2)->default(0.00);    // km × rate_per_km
            $table->decimal('multiplier_applied', 5, 2)->default(1.00);  // jo multiplier laga
            $table->decimal('subtotal', 10, 2)->default(0.00);           // sab charges ka total
            $table->decimal('tax_amount', 10, 2)->default(0.00);         // GST etc
            $table->decimal('discount_amount', 10, 2)->default(0.00);    // agar koi discount
            $table->decimal('total_payment', 10, 2)->default(0.00);      // final amount

            // ---------- DATES ----------
            $table->date('requested_pickup_date');                        // user ne jo date maangi
            $table->date('confirmed_pickup_date')->nullable();            // admin ne jo confirm ki
            $table->date('expected_delivery_date')->nullable();           // expected delivery
            $table->date('actual_delivery_date')->nullable();             // actual deliver hui

            // ---------- ADMIN ----------
            $table->enum('admin_status', [
                'pending',      // naya request
                'reviewed',     // admin ne dekha
                'approved',     // approved
                'dispatched',   // maal rawan hogaya
                'delivered',    // deliver hogaya
                'cancelled',    // cancel
                'rejected',     // reject
            ])->default('pending');
            $table->text('admin_description')->nullable();                // admin ka note
            $table->foreignId('assigned_to')->nullable()                  // kaun sa driver/staff
                  ->constrained('users')->onDelete('set null');

            // ---------- USER STATUS ----------
            $table->enum('user_status', [
                'pending',          // wait kar raha
                'confirmed',        // confirm hogaya
                'in_transit',       // raste mein hai
                'delivered',        // mil gaya
                'cancelled',        // user ne cancel kiya
            ])->default('pending');

            // ---------- PAYMENT ----------
            $table->enum('payment_status', [
                'unpaid',
                'partial',
                'paid',
                'refunded',
            ])->default('unpaid');
            $table->enum('payment_method', [
                'cash',
                'online',
                'upi',
                'bank_transfer',
            ])->nullable();
            $table->string('transaction_id')->nullable();

            // ---------- EXTRA ----------
            $table->text('special_instructions')->nullable();             // user ka koi note
            $table->string('tracking_number')->unique()->nullable();      // tracking ke liye

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_leads');
    }
};
