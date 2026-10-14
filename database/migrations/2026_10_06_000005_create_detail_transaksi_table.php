<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id('id_detail');

            $table->foreignId('id_transaksi')
                ->constrained('transaksi', 'id_transaksi')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('id_layanan')
                ->constrained('layanan', 'id_layanan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('berat_kg', 6, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};
