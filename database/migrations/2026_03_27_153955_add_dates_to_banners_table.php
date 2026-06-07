<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->datetime('start_date')->nullable()->after('banner_video_status');
            $table->datetime('end_date')->nullable()->after('start_date');
        });
    }

    public function down()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};