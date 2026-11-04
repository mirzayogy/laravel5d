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
    Schema::create('accounts', function (Blueprint $table) {
        $table->id();
        // Karena sistem belum memiliki fitur Login utuh, beri nilai nullable() sementara pada user_id
        $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
        $table->string('name'); 
        $table->decimal('balance', 15, 2)->default(0);
        $table->string('type'); 
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
