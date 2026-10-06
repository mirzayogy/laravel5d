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
        Schema::create('restock_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fish_id')->constrained('fishes')->cascadeOnDelete();
            $table->time('remind_at');
            $table->json('days_of_week')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restock_reminders');
    }
};
