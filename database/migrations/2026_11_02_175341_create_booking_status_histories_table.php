
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_status_histories', function (Blueprint $table) {
            $table->id();

            // Booking yang mengalami perubahan status
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->restrictOnDelete();

            // Pengguna yang mengubah status
            $table->foreignId('changed_by')
                ->constrained('users')
                ->restrictOnDelete();

            // Status sebelum perubahan
            $table->enum('old_status', [
                'pending',
                'confirmed',
                'in_progress',
                'completed',
                'cancelled'
            ])->nullable();

            // Status setelah perubahan
            $table->enum('new_status', [
                'pending',
                'confirmed',
                'in_progress',
                'completed',
                'cancelled'
            ]);

            // Catatan perubahan status
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_status_histories');
    }
};
