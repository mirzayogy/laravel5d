<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hint_unlocks', function (Blueprint $table) {
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hint_id')->constrained()->cascadeOnDelete();
            $table->dateTime('unlocked_at');
            $table->primary(['team_id', 'hint_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hint_unlocks');
    }
};
