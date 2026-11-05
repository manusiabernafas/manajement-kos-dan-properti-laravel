<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique();
            $table->foreignId('property_id')->constrained()->restrictOnDelete();
            $table->enum('category', ['electricity', 'water', 'internet', 'salary', 'maintenance', 'cleaning', 'security', 'tax', 'furniture', 'marketing', 'operational', 'other']);
            $table->date('date');
            $table->text('description');
            $table->decimal('amount', 14, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'qris', 'e_wallet', 'virtual_account', 'other'])->nullable();
            $table->string('vendor')->nullable();
            $table->string('attachment_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('category');
            $table->index('date');
        });

        Schema::create('maintenance_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('property_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('category');
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('estimated_cost', 14, 2)->nullable();
            $table->decimal('actual_cost', 14, 2)->nullable();
            $table->enum('status', ['open', 'assigned', 'in_progress', 'waiting', 'resolved', 'closed', 'cancelled'])->default('open');
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('priority');
        });

        Schema::create('maintenance_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('comment');
            $table->timestamps();
        });

        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignId('property_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->string('category');
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'assigned', 'in_progress', 'waiting', 'resolved', 'closed', 'cancelled'])->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('priority');
        });

        Schema::create('complaint_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('comment');
            $table->timestamps();
        });

        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_code')->unique();
            $table->foreignId('property_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->string('category');
            $table->string('name');
            $table->string('serial_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 14, 2)->nullable();
            $table->enum('condition', ['good', 'minor_damage', 'major_damage', 'lost'])->default('good');
            $table->enum('status', ['active', 'maintenance', 'lost', 'retired'])->default('active');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('condition');
        });

        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained()->cascadeOnDelete();
            $table->string('event_type');
            $table->string('from_value')->nullable();
            $table->string('to_value')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_histories');
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('complaint_comments');
        Schema::dropIfExists('complaints');
        Schema::dropIfExists('maintenance_comments');
        Schema::dropIfExists('maintenance_tickets');
        Schema::dropIfExists('expenses');
    }
};
