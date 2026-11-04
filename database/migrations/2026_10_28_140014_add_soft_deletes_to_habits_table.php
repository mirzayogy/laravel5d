<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A deleted habit stays recoverable for a while so the "Undo" in the toast can bring it back.
     */
    public function up(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
