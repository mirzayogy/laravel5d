<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('components', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('category');

            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('interface')->nullable();

            $table->json('specifications')->nullable();

            $table->unsignedInteger('quantity_owned')->default(1);

            $table->string('storage_location')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
