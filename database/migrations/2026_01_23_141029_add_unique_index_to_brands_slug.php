<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            // Drop existing unique index if exists
            $table->dropUnique('brands_slug_unique');
            
            // Add composite unique index on slug and deleted_at
            $table->unique(['slug', 'deleted_at'], 'brands_slug_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            // Drop composite unique index
            $table->dropUnique('brands_slug_deleted_at_unique');
            
            // Recreate original unique index
            $table->unique('slug', 'brands_slug_unique');
        });
    }
};
