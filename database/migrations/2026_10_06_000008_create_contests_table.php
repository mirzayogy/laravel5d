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
        Schema::create('contests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained()->restrictOnDelete();
            $table->foreignId('chief_judge_id')->nullable()->constrained('judges')->nullOnDelete();
            $table->string('name');
            $table->string('venue');
            $table->string('city');
            $table->date('held_on')->index();
            $table->unsignedInteger('registration_fee')->default(0);
            $table->enum('status', ['upcoming', 'ongoing', 'finished'])->default('upcoming');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contests');
    }
};
