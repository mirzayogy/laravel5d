<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('delivery_fee', 12, 2)->default(10000);
            $table->enum('status', ['pending', 'confirmed', 'processing', 'delivered', 'cancelled'])->default('pending');
            $table->text('delivery_address');
            $table->string('payment_method')->default('qris');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('paid');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
