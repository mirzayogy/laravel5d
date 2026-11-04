
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiment_components', function (Blueprint $table) {
            $table->id();

            $table->foreignId('experiment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('component_id')
                ->constrained()
                ->restrictOnDelete();

            $table->unsignedInteger('quantity_used');

            $table->string('role');

            $table->json('connection_details')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['experiment_id', 'component_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiment_components');
    }
};
