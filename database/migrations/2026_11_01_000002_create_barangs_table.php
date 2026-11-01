<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')
                ->constrained('kategoris')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('nama_barang', 150);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_sewa_per_hari', 12, 2);
            $table->unsignedInteger('stok')->default(0);
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
