
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiment_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('experiment_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->text('summary');
            $table->text('conclusion');

            $table->text('limitations')->nullable();
            $table->text('recommendations')->nullable();

            $table->timestamp('generated_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiment_reports');
    }
};
