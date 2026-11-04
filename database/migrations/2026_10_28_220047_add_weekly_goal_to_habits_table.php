<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "3 days a week" instead of every day. Null keeps the habit daily, as before.
     */
    public function up(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            $table->unsignedTinyInteger('weekly_goal')->nullable()->after('unit');
        });
    }

    public function down(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            $table->dropColumn('weekly_goal');
        });
    }
};
