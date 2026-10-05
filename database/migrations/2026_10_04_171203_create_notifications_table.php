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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('task_id')
                ->nullable()
                ->constrained('tasks')
                ->cascadeOnDelete();

            $table->text('message');

            $table->dateTime('notification_time');

            $table->boolean('is_read')->default(false);

            $table->enum('type', [
                'reminder',
                'deadline',
                'warning',
                'rescue'
            ])->default('reminder');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
