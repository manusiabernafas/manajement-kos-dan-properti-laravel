<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('target_type', ['all', 'property', 'building', 'floor', 'room'])->default('all');
            $table->foreignId('property_id')->nullable()->constrained('properties')->cascadeOnDelete();
            $table->foreignId('building_id')->nullable()->constrained('buildings')->cascadeOnDelete();
            $table->foreignId('floor_id')->nullable()->constrained('floors')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->cascadeOnDelete();
            $table->timestamp('publish_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('published');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('target_type');
        });

        Schema::create('announcement_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['announcement_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_reads');
        Schema::dropIfExists('announcements');
    }
};
