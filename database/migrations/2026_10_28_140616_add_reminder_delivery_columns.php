<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reminder delivery: a per-person switch for emails, and a marker so
     * a reminder is sent at most once per day.
     */
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->boolean('email_reminders')->default(true)->after('daily_goal');
        });

        Schema::table('reminders', function (Blueprint $table) {
            $table->date('last_sent_on')->nullable()->after('is_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('reminders', fn (Blueprint $table) => $table->dropColumn('last_sent_on'));
        Schema::table('profiles', fn (Blueprint $table) => $table->dropColumn('email_reminders'));
    }
};
