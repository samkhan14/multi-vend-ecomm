<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add vendor_id to orders table
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                $table->index('vendor_id');
            }
        });
        
        // Add vendor_id to order_items table
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('order_id');
                $table->index('vendor_id');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });
        
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });
    }
};
