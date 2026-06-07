<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('sale_price', 10, 2)->nullable()->after('product_price');
            $table->datetime('sale_start_date')->nullable()->after('sale_price');  
            $table->datetime('sale_end_date')->nullable()->after('sale_start_date'); 
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sale_price', 'sale_start_date', 'sale_end_date']);
        });
    }
};