<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('view_count')->default(0)->after('stock');
            $table->integer('interaction_count')->default(0)->after('view_count');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('view_count');
            $table->dropColumn('interaction_count');
        });
    }
};