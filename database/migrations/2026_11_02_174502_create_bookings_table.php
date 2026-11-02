
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

            $table->string('booking_code')->unique();

            // Customer yang membuat booking
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Hewan yang akan grooming
            $table->foreignId('pet_id')
                ->constrained('pets')
                ->restrictOnDelete();

            // Layanan grooming
            $table->foreignId('service_id')
                ->constrained('services')
                ->restrictOnDelete();

            // Groomer yang ditugaskan admin
            $table->foreignId('groomer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Jadwal grooming
            $table->dateTime('scheduled_at');
            $table->dateTime('ends_at');

            // Harga saat booking dibuat
            $table->decimal('total_price', 12, 2);

            // Status booking
            $table->enum('status', [
                'pending',
                'confirmed',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('pending');

            // Catatan customer
            $table->text('customer_notes')->nullable();

            // Alasan pembatalan atau penolakan
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            // Membantu pencarian jadwal groomer
            $table->index([
                'groomer_id',
                'scheduled_at',
                'ends_at'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
