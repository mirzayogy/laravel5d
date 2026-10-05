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
       Schema::create('specification_types', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->string('slug');
    $table->string('unit')->nullable();
    $table->string('data_type')->default('string'); // string, number, boolean
    $table->boolean('is_filterable')->default(false);
    $table->timestamps();

    $table->unique(['category_id', 'slug']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specification_types');
    }
};
