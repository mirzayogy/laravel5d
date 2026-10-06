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
        Schema::create('sales_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fish_id')->constrained('fishes')->cascadeOnDelete();
            $table->date('logged_date');
            $table->unsignedInteger('value')->default(1);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['fish_id', 'logged_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_logs');
    }
};
