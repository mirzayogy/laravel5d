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
        Schema::create('training_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bird_id')->constrained()->cascadeOnDelete();
            $table->date('trained_on');
            $table->enum('focus', ['volume', 'mental', 'stamina', 'mimicry']);
            $table->unsignedSmallInteger('duration_minutes');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['bird_id', 'trained_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_logs');
    }
};
