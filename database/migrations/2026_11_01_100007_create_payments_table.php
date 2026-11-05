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
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            // pemilik yang memverifikasi pembayaran
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['deposit', 'rent', 'late_fee', 'refund'])->default('rent');
            $table->unsignedInteger('amount');
            $table->date('due_date');
            $table->enum('method', ['cash', 'transfer', 'qris'])->nullable();
            $table->enum('status', ['unpaid', 'waiting_verification', 'paid', 'overdue'])->default('unpaid');
            $table->string('proof_path')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};