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
        Schema::create('judge_species', function (Blueprint $table) {
            $table->foreignId('judge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('species_id')->constrained('species')->cascadeOnDelete();

            $table->primary(['judge_id', 'species_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('judge_species');
    }
};
