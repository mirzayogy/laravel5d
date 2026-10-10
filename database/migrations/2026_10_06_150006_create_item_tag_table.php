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
        Schema::create('book_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->unique(['book_id', 'tag_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_tag');
    }
};
