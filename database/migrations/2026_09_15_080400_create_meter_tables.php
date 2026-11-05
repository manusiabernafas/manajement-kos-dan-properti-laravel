<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('meter_number')->nullable();
            $table->enum('meter_type', ['electricity', 'water']);
            $table->string('unit')->default('kWh');
            $table->decimal('tariff', 14, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['room_id', 'meter_type']);
        });

        Schema::create('meter_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_id')->constrained()->cascadeOnDelete();
            $table->date('reading_date');
            $table->decimal('previous_value', 14, 2);
            $table->decimal('current_value', 14, 2);
            $table->decimal('usage', 14, 2);
            $table->decimal('tariff', 14, 2);
            $table->decimal('total', 14, 2);
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('reading_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meter_readings');
        Schema::dropIfExists('meters');
    }
};
