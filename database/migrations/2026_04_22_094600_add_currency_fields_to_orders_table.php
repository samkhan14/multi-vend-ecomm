<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_currency')->nullable()->after('grand_total');
            $table->decimal('conversion_rate', 10, 4)->nullable()->after('order_currency');
            $table->decimal('base_amount', 10, 2)->nullable()->after('conversion_rate');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_currency', 'conversion_rate', 'base_amount']);
        });
    }
};