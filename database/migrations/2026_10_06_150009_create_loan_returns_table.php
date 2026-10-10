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
        Schema::create('loan_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete()->unique();
            $table->date('actual_return_date');
            $table->integer('late_days')->default(0);
            $table->decimal('penalty_fee', 12, 2)->default(0);
            $table->text('condition_after')->nullable();
            $table->string('return_status')->default('received'); // received, verified, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_returns');
    }
};
