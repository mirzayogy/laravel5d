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
        Schema::create('loan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('fee_per_day', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->string('condition_out')->default('Baik');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_details');
    }
};
