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
        Schema::create('birds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('species_id')->constrained('species')->restrictOnDelete();
            $table->foreignId('sire_id')->nullable()->constrained('birds')->nullOnDelete();
            $table->foreignId('dam_id')->nullable()->constrained('birds')->nullOnDelete();
            $table->string('name');
            $table->string('ring_number')->unique();
            $table->enum('sex', ['male', 'female', 'unknown'])->default('unknown');
            $table->date('hatched_on')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birds');
    }
};
