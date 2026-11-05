<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kost_id')->constrained()->cascadeOnDelete();
            $table->string('room_number');
            $table->unsignedTinyInteger('floor')->default(1);
            $table->decimal('size_m2', 5, 1)->default(9);
            $table->unsignedInteger('price_per_day');
            $table->unsignedInteger('price_per_month');
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->timestamps();

            // nomor kamar unik di dalam satu kost
            $table->unique(['kost_id', 'room_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};