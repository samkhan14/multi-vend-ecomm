<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vendor_bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('account_title');
            $table->string('iban_number')->unique(); // Unique hi rahega
            $table->string('bank_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_bank_details');
    }
};