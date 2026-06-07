<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add new unique constraint that includes deleted_at
            $table->unique(['vendor_id', 'product_slug', 'deleted_at'], 'products_vendor_slug_deleted_unique');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_vendor_slug_deleted_unique');
        });
    }
};
