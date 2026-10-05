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
        Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
    $table->foreignId('shipping_address_id')->constrained('addresses')->restrictOnDelete();
    $table->string('order_number')->unique();
    $table->string('status')->default('pending'); // pending, paid, shipped, completed, cancelled
    $table->decimal('shipping_cost', 12, 2)->default(0);
    $table->decimal('total_amount', 12, 2)->default(0);
    $table->timestamp('ordered_at')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
