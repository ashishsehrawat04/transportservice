<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('transport_lead_id')->nullable()->constrained('transport_leads')->onDelete('set null');
            $table->string('invoice_number')->nullable()->index();
            $table->string('tracking_number')->nullable()->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_mobile')->nullable();
            $table->string('item_name');
            $table->string('item_type')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('length_cm', 8, 2)->nullable();
            $table->decimal('width_cm', 8, 2)->nullable();
            $table->decimal('height_cm', 8, 2)->nullable();
            $table->decimal('weight_kg', 8, 2)->default(0);
            $table->decimal('volume_cft', 8, 2)->nullable();
            $table->string('from_city')->nullable();
            $table->string('to_city')->nullable();
            $table->decimal('distance_km', 10, 2)->default(0);
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('weight_charge', 10, 2)->default(0);
            $table->decimal('volume_charge', 10, 2)->default(0);
            $table->decimal('distance_charge', 10, 2)->default(0);
            $table->decimal('multiplier_applied', 5, 2)->default(1);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_payment', 10, 2)->default(0);
            $table->string('admin_status')->default('pending');
            $table->string('user_status')->default('pending');
            $table->string('payment_status')->default('unpaid');
            $table->date('requested_pickup_date')->nullable();
            $table->date('confirmed_pickup_date')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->text('admin_description')->nullable();
            $table->text('special_instructions')->nullable();
            $table->json('quote_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_quotes');
    }
};
