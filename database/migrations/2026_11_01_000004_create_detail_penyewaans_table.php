<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penyewaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyewaan_id')
                ->constrained('penyewaans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('barang_id')
                ->constrained('barangs')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->unsignedInteger('jumlah');
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            // satu barang hanya muncul satu kali per transaksi sewa
            $table->unique(['penyewaan_id', 'barang_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penyewaans');
    }
};
