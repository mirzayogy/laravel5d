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
        Schema::create('booking_spare_part', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spare_part_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal_price', 12, 2);
            $table->timestamps();

            $table->unique(['service_booking_id', 'spare_part_id'], 'booking_part_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_spare_part');
    }
};
