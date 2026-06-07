<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Products table indexes - Check before adding
        Schema::table('products', function (Blueprint $table) {
            // Check if index exists before adding
            if (!Schema::hasIndex('products', ['status', 'category_id'])) {
                $table->index(['status', 'category_id']);
            }
            if (!Schema::hasIndex('products', ['product_slug'])) {
                $table->index('product_slug');
            }
            if (!Schema::hasIndex('products', ['status', 'is_featured'])) {
                $table->index(['status', 'is_featured']);
            }
            if (!Schema::hasIndex('products', ['vendor_id'])) {
                $table->index('vendor_id');
            }
            if (!Schema::hasIndex('products', ['created_at'])) {
                $table->index('created_at');
            }
        });

        // Ratings table indexes
        Schema::table('ratings', function (Blueprint $table) {
            if (!Schema::hasIndex('ratings', ['product_id', 'status'])) {
                $table->index(['product_id', 'status']);
            }
            if (!Schema::hasIndex('ratings', ['created_at'])) {
                $table->index('created_at');
            }
        });

        // Carts table indexes
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasIndex('carts', ['session_id'])) {
                $table->index('session_id');
            }
            if (!Schema::hasIndex('carts', ['user_id'])) {
                $table->index('user_id');
            }
        });

        // Categories table indexes
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasIndex('categories', ['status', 'level'])) {
                $table->index(['status', 'level']);
            }
            if (!Schema::hasIndex('categories', ['url'])) {
                $table->index('url');
            }
            if (!Schema::hasIndex('categories', ['parent_id'])) {
                $table->index('parent_id');
            }
        });

        // Orders table indexes - Skip if already exists
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasIndex('orders', ['order_number'])) {
                $table->index('order_number');
            }
            if (!Schema::hasIndex('orders', ['user_id'])) {
                $table->index('user_id');
            }
            if (!Schema::hasIndex('orders', ['vendor_id'])) {
                $table->index('vendor_id');
            }
            if (!Schema::hasIndex('orders', ['status'])) {
                $table->index('status');
            }
            if (!Schema::hasIndex('orders', ['created_at'])) {
                $table->index('created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['status', 'category_id']);
            $table->dropIndex(['product_slug']);
            $table->dropIndex(['status', 'is_featured']);
            $table->dropIndex(['vendor_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['session_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['status', 'level']);
            $table->dropIndex(['url']);
            $table->dropIndex(['parent_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['order_number']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['vendor_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });
    }
};