<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->foreignId('property_id')->constrained()->restrictOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained('contracts')->nullOnDelete();
            $table->date('issue_date');
            $table->date('due_date');
            $table->unsignedTinyInteger('period_month')->nullable();
            $table->unsignedSmallInteger('period_year')->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('penalty', 14, 2)->default(0);
            $table->decimal('tax', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('remaining_amount', 14, 2)->default(0);
            $table->enum('status', ['draft', 'unpaid', 'partial', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index(['contract_id', 'period_year', 'period_month'], 'invoices_billing_period_idx');
            $table->index('due_date');
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->enum('item_type', ['rent', 'electricity', 'water', 'internet', 'parking', 'laundry', 'cleaning', 'penalty', 'other'])->default('rent');
            $table->string('description');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('price', 14, 2);
            $table->decimal('subtotal', 14, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('invoice_id')->constrained()->restrictOnDelete();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 14, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'qris', 'e_wallet', 'virtual_account', 'other'])->default('cash');
            $table->string('reference_number')->nullable();
            $table->string('proof_path')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignId('contract_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 14, 2);
            $table->date('received_date');
            $table->decimal('refund_amount', 14, 2)->nullable();
            $table->decimal('deduction', 14, 2)->default(0);
            $table->date('refund_date')->nullable();
            $table->enum('status', ['held', 'partially_refunded', 'refunded', 'forfeited'])->default('held');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('penalty_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('properties')->cascadeOnDelete();
            $table->enum('calculation_type', ['fixed', 'per_day', 'percentage'])->default('fixed');
            $table->decimal('amount', 14, 2);
            $table->unsignedInteger('grace_period_days')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('penalty_config_id')->nullable()->constrained('penalty_configs')->nullOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('reason')->nullable();
            $table->date('applied_date');
            $table->timestamps();

            $table->unique('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penalties');
        Schema::dropIfExists('penalty_configs');
        Schema::dropIfExists('deposits');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
