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
        Schema::create('transport_auth_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('email_login_enabled')->default(true);
            $table->boolean('mobile_login_enabled')->default(true);
            $table->boolean('google_login_enabled')->default(false);
            $table->boolean('admin_approval_required')->default(true);
            $table->timestamps();
        });

        Schema::create('user_login_otps', function (Blueprint $table) {
            $table->id();
            $table->string('mobile', 15)->index();
            $table->string('otp_hash');
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('transport_lead_id')->nullable()->constrained('transport_leads')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['cash', 'online', 'upi', 'bank_transfer', 'wallet'])->default('cash');
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('transport_lead_id')->nullable()->constrained('transport_leads')->onDelete('set null');
            $table->enum('type', ['credit', 'debit', 'refund']);
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_after', 10, 2)->default(0);
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('transport_lead_id')->nullable()->constrained('transport_leads')->onDelete('set null');
            $table->foreignId('shipment_payment_id')->nullable()->constrained('shipment_payments')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['requested', 'approved', 'rejected', 'processed'])->default('requested');
            $table->text('reason')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_refunds');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('shipment_payments');
        Schema::dropIfExists('user_login_otps');
        Schema::dropIfExists('transport_auth_settings');
    }
};
