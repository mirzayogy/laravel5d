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
        Schema::create('booking_service_package', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_package_id')->constrained()->cascadeOnDelete();
            $table->decimal('package_price', 12, 2);
            $table->text('technician_notes')->nullable();
            $table->timestamps();

            $table->unique(['service_booking_id', 'service_package_id'], 'booking_pkg_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_service_package');
    }
};
