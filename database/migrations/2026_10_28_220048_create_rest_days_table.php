<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A day the user chose to rest: it neither counts nor breaks the streak.
     * One per person per ISO week is enforced by the controller; one per date by the unique key.
     */
    public function up(): void
    {
        Schema::create('rest_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('rest_date');
            $table->timestamps();

            $table->unique(['user_id', 'rest_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rest_days');
    }
};
