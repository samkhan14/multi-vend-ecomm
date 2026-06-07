<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('carts', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('product_id');
        $table->unsignedBigInteger('product_variant_id')->nullable();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('session_id')->nullable();
        $table->integer('quantity')->default(1);
        $table->decimal('price', 10, 2);
        $table->timestamps();
        
        $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
