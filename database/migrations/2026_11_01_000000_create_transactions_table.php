<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->string('type'); // income | expense
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['transaction_date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
