
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grooming_records', function (Blueprint $table) {
            $table->id();

            // Booking yang sedang dikerjakan
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->restrictOnDelete();

            // Groomer yang mengerjakan
            $table->foreignId('groomer_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Catatan hasil grooming
            $table->text('notes')->nullable();

            // Kondisi hewan setelah grooming
            $table->text('condition_notes')->nullable();

            // Waktu grooming selesai
            $table->dateTime('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grooming_records');
    }
};
