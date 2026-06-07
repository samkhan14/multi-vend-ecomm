<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // old global unique slug index remove (INDEX NAME)
            $table->dropUnique('products_product_slug_unique');

            // vendor + slug unique
            $table->unique(
                ['vendor_id', 'product_slug'],
                'products_vendor_product_slug_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->dropUnique('products_vendor_product_slug_unique');

            // restore old behaviour
            $table->unique('product_slug', 'products_product_slug_unique');
        });
    }
};
