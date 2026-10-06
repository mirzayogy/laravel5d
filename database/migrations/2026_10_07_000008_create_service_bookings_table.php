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
        Schema::create('service_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mechanic_id')->nullable()->constrained()->nullOnDelete();
            $table->date('booking_date');
            $table->time('booking_time');
            $table->enum('status', ['Pending', 'Confirmed', 'In Progress', 'Completed', 'Cancelled'])->default('Pending');
            $table->text('customer_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_bookings');
    }
};
