<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_products', function (Blueprint $table) {
            $table->id();
            $table->biginteger('ad_id')->unsigned();
            $table->foreign('ad_id', 'ad_id')->references('id')->on('ads')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->biginteger('product_id')->unsigned();
            $table->foreign('product_id', 'ad_product_id')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->boolean('is_active')->default(0)->comment('0=inactive;1=active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ad_products');
    }
}
