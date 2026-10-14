<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');

            $table->foreignId('id_pelanggan')
                ->constrained('pelanggan', 'id_pelanggan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('id_karyawan')
                ->constrained('karyawan', 'id_karyawan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('tgl_masuk');
            $table->date('tgl_selesai')->nullable();
            $table->enum('status', ['diterima', 'dicuci', 'selesai', 'diambil'])
                ->default('diterima');
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
