<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // dipakai untuk mencocokkan penyewa dengan jenis kost
            $table->enum('gender', ['laki-laki', 'perempuan']);
            $table->string('phone');
            $table->string('id_number')->unique();
            $table->string('occupation');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};