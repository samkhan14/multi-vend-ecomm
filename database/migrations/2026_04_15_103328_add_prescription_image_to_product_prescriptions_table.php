<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_prescriptions', function (Blueprint $table) {
            $table->string('prescription_image')->nullable()->after('notes');
        });
    }

    public function down()
    {
        Schema::table('product_prescriptions', function (Blueprint $table) {
            $table->dropColumn('prescription_image');
        });
    }
};