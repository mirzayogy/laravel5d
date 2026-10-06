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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('service_booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('total_services_amount', 12, 2)->default(0);
            $table->decimal('total_parts_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->enum('payment_method', ['Cash', 'Transfer', 'QRIS', 'Credit Card'])->default('Cash');
            $table->enum('payment_status', ['Unpaid', 'Paid', 'Refunded'])->default('Unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
