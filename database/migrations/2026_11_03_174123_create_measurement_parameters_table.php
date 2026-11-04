
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measurement_parameters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('experiment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('source_component_id')
                ->nullable()
                ->constrained('components')
                ->restrictOnDelete();

            $table->string('name');
            $table->string('symbol')->nullable();
            $table->string('unit');

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('measurement_parameters');
    }
};
