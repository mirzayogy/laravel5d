<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyewaan_id')
                ->unique() // relasi one-to-one dengan penyewaans
                ->constrained('penyewaans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->date('tanggal_bayar');
            $table->decimal('jumlah_bayar', 12, 2);
            $table->enum('metode', ['tunai', 'transfer', 'e_wallet']);
            $table->enum('status', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->string('bukti_pembayaran')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
