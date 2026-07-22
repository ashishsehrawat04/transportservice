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
        Schema::create('warehouse_leads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('item_name');
            $table->string('item_type')->nullable();
            $table->integer('quantity')->default(1);

            $table->decimal('length_cm', 8, 2)->nullable();
            $table->decimal('width_cm', 8, 2)->nullable();
            $table->decimal('height_cm', 8, 2)->nullable();
            $table->decimal('weight_kg', 8, 2);
            $table->decimal('volume_cft', 8, 2)->nullable();

            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('restrict');

            $table->decimal('base_price', 10, 2)->default(0.00);
            $table->string('calculation_type')->nullable();
            $table->decimal('weight_charge', 10, 2)->default(0.00);
            $table->decimal('volume_charge', 10, 2)->default(0.00);
            $table->decimal('multiplier_applied', 5, 2)->default(1.00);
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total_payment', 10, 2)->default(0.00);

            $table->date('requested_pickup_date');
            $table->unsignedInteger('storage_days')->default(1);

            $table->enum('admin_status', [
                'pending',
                'reviewed',
                'approved',
                'dispatched',
                'delivered',
                'cancelled',
                'rejected',
            ])->default('pending');
            $table->text('admin_description')->nullable();
            $table->foreignId('assigned_to')->nullable()
                  ->constrained('users')->onDelete('set null');

            $table->enum('user_status', [
                'pending',
                'confirmed',
                'in_transit',
                'delivered',
                'cancelled',
            ])->default('pending');

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

            $table->text('special_instructions')->nullable();
            $table->string('tracking_number')->unique()->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_leads');
    }
};
