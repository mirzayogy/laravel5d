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
        Schema::create('symptom_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('symptom_id')->constrained()->restrictOnDelete();
            $table->date('log_date');
            $table->unsignedTinyInteger('severity')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'symptom_id', 'log_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('symptom_logs');
    }
};
