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
        Schema::create('inspection_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('odometer_km');
            $table->enum('fuel_level', ['Empty', 'Quarter', 'Half', 'Three Quarters', 'Full'])->default('Half');
            $table->enum('brake_condition', ['Good', 'Fair', 'Needs Replacement'])->default('Good');
            $table->enum('tire_condition', ['Good', 'Fair', 'Needs Replacement'])->default('Good');
            $table->enum('battery_condition', ['Good', 'Fair', 'Weak'])->default('Good');
            $table->text('inspector_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_reports');
    }
};
