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
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            // perpanjangan: menunjuk booking sebelumnya
            $table->foreignId('previous_booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->string('code')->unique();
            $table->enum('rental_type', ['daily', 'monthly'])->default('monthly');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            // hari untuk daily, bulan untuk monthly
            $table->unsignedSmallInteger('duration')->default(1);
            // tarif disalin saat booking, jadi perubahan harga tidak memengaruhi booking lama
            $table->unsignedInteger('price_per_unit');
            $table->unsignedInteger('deposit_amount')->default(0);
            $table->unsignedInteger('total_price');
            // batas bayar saat status pending
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('checked_in_at')->nullable();
            $table->dateTime('checked_out_at')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'active', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};