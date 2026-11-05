<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('address');
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('description')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('google_maps_url')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->text('rules')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->text('description')->nullable();
            $table->unsignedInteger('number_of_floors')->default(1);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['property_id', 'code']);
            $table->index('status');
        });

        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('floor_number');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['building_id', 'floor_number']);
            $table->index('status');
        });

        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->text('description')->nullable();
            $table->decimal('monthly_price', 14, 2);
            $table->decimal('weekly_price', 14, 2)->nullable();
            $table->decimal('daily_price', 14, 2)->nullable();
            $table->decimal('deposit_amount', 14, 2)->default(0);
            $table->unsignedInteger('capacity')->default(1);
            $table->string('size')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['property_id', 'code']);
            $table->index('status');
        });

        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->default('general');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('category');
            $table->index('status');
        });

        Schema::create('facilitables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->morphs('facilitable');
            $table->timestamps();

            $table->unique(['facility_id', 'facilitable_id', 'facilitable_type'], 'facilitables_unique');
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->foreignId('floor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained()->restrictOnDelete();
            $table->string('room_number');
            $table->string('room_name')->nullable();
            $table->decimal('price', 14, 2);
            $table->decimal('deposit', 14, 2)->default(0);
            $table->unsignedInteger('capacity')->default(1);
            $table->text('description')->nullable();
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance', 'inactive'])->default('available');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['property_id', 'room_number']);
            $table->index('status');
        });

        Schema::create('property_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'property_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_user');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('facilitables');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('floors');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('properties');
    }
};
