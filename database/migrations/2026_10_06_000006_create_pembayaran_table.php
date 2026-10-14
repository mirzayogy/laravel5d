<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');

            $table->foreignId('id_transaksi')
                ->constrained('transaksi', 'id_transaksi')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->date('tgl_bayar');
            $table->enum('metode', ['tunai', 'transfer', 'qris'])->default('tunai');
            $table->decimal('jumlah_bayar', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
