
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measurement_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('experiment_run_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('measurement_parameter_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('sample_no');

            $table->decimal('measured_value', 15, 6);

            $table->decimal('reference_value', 15, 6)->nullable();

            $table->timestamp('measured_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(
                ['experiment_run_id', 'measurement_parameter_id', 'sample_no'],
                'mr_run_param_sample_unique'
);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('measurement_records');
    }
};
