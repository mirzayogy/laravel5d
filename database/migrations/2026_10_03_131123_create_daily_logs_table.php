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
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('log_date');
            $table->decimal('basal_temp', 4, 2)->nullable();
            $table->string('discharge')->nullable();
            $table->string('sex_activity')->nullable();
            $table->string('ovulation_test')->nullable();
            $table->string('pregnancy_test')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->unsignedSmallInteger('water_ml')->nullable();
            $table->decimal('sleep_hours', 3, 1)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'log_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_logs');
    }
};
