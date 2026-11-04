
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiment_runs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('experiment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('run_number');

            $table->string('status')->default('planned');

            $table->string('firmware_version')->nullable();
            $table->string('firmware_revision')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->json('environment_conditions')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['experiment_id', 'run_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiment_runs');
    }
};
