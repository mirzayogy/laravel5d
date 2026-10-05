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
    Schema::create('product_specifications', function (Blueprint $table) {
        $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        $table->foreignId('specification_type_id')->constrained()->cascadeOnDelete();
        $table->string('value');
        $table->primary(['product_id', 'specification_type_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_specifications');
    }
};
