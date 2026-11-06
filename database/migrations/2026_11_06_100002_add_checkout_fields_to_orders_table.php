<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_type')->default('dine_in')->after('status');
            $table->string('table_number', 10)->nullable()->after('order_type');
            $table->string('payment_method')->nullable()->after('table_number');
            $table->string('payment_status')->default('unpaid')->after('payment_method');
            $table->string('payment_reference')->nullable()->after('payment_status');
            $table->dateTime('paid_at')->nullable()->after('payment_reference');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_type', 'table_number', 'payment_method', 'payment_status', 'payment_reference', 'paid_at']);
        });
    }
};
