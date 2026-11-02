<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot Many-to-Many: Station <-> Game
        Schema::create('game_station', function (Blueprint $table) {
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->primary(['station_id', 'game_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_station');
    }
};
