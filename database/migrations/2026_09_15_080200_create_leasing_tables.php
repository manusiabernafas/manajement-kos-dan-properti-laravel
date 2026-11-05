<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->unique();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->date('booking_date');
            $table->date('check_in_date');
            $table->date('expected_checkout_date')->nullable();
            $table->decimal('monthly_price', 14, 2);
            $table->decimal('deposit', 14, 2)->default(0);
            $table->enum('status', ['inquiry', 'reserved', 'confirmed', 'cancelled', 'expired', 'checked_in'])->default('inquiry');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->foreignId('property_id')->constrained()->restrictOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('monthly_price', 14, 2);
            $table->decimal('deposit', 14, 2)->default(0);
            $table->unsignedTinyInteger('billing_day')->default(1);
            $table->text('terms')->nullable();
            $table->enum('status', ['draft', 'active', 'expired', 'terminated'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->restrictOnDelete();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->date('check_in_date');
            $table->decimal('monthly_price', 14, 2);
            $table->decimal('deposit', 14, 2)->default(0);
            $table->date('billing_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('check_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->restrictOnDelete();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->date('checkout_date');
            $table->decimal('outstanding_amount', 14, 2)->default(0);
            $table->decimal('penalty_amount', 14, 2)->default(0);
            $table->decimal('damage_cost', 14, 2)->default(0);
            $table->decimal('deposit_amount', 14, 2)->default(0);
            $table->decimal('refund_amount', 14, 2)->default(0);
            $table->decimal('final_amount', 14, 2)->default(0);
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_outs');
        Schema::dropIfExists('check_ins');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('bookings');
    }
};
