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
       Schema::create('compatibility_rules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('compatibility_type_id')->constrained()->cascadeOnDelete();
    $table->foreignId('source_category_id')->constrained('categories')->cascadeOnDelete();
    $table->foreignId('target_category_id')->constrained('categories')->cascadeOnDelete();
    $table->foreignId('source_spec_type_id')->constrained('specification_types')->cascadeOnDelete();
    $table->foreignId('target_spec_type_id')->constrained('specification_types')->cascadeOnDelete();
    $table->string('operator'); // equals, contains, gte, lte
    $table->string('description')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compatibility_rules');
    }
};
