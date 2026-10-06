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
        Schema::create('bird_contest', function (Blueprint $table) {
            $table->foreignId('bird_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contest_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('entry_number');
            $table->enum('division', ['novice', 'intermediate', 'champion']);
            $table->decimal('total_score', 6, 2)->nullable();
            $table->unsignedSmallInteger('final_rank')->nullable();
            $table->timestamp('registered_at')->nullable();

            $table->primary(['bird_id', 'contest_id']);
            $table->unique(['contest_id', 'entry_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bird_contest');
    }
};
