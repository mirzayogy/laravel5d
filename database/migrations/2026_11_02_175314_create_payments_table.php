
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->unique()
                ->constrained('bookings')
                ->restrictOnDelete();

            $table->enum('method', [
                'cash',
                'bank_transfer',
                'ewallet'
            ]);

            $table->decimal('amount', 12, 2);

            $table->dateTime('paid_at')->nullable();

            $table->string('proof_image')->nullable();

            $table->enum('status', [
                'unpaid',
                'waiting_verification',
                'paid',
                'rejected'
            ])->default('unpaid');

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('verified_at')->nullable();

            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
