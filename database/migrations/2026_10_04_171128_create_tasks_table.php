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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('deadline')->nullable();

            $table->enum('difficulty', [
                'easy',
                'medium',
                'hard'
            ])->default('medium');

            $table->unsignedInteger('estimated_minutes')->nullable();

            $table->decimal('priority_score', 5, 2)
                ->default(0);

            $table->enum('status', [
                'pending',
                'in_progress',
                'completed',
                'delayed'
            ])->default('pending');

            $table->dateTime('completed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
