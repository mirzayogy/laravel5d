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
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bird_id')->constrained()->cascadeOnDelete();
            $table->date('checked_on');
            $table->enum('type', ['checkup', 'vaccination', 'treatment', 'molting']);
            $table->unsignedSmallInteger('weight_grams')->nullable();
            $table->string('diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['bird_id', 'checked_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};
