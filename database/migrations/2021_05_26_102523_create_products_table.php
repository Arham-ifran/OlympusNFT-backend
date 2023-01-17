<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->biginteger('store_id')->unsigned();
            $table->foreign('store_id', 'store_id')->references('id')->on('stores')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->biginteger('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onUpdate('RESTRICT')->onDelete('SET NULL');
            $table->integer('auction_length_id')->nullable();
            $table->foreign('auction_length_id')->references('id')->on('auction_lengths')->onUpdate('RESTRICT')->onDelete('SET NULL');
            $table->string('title', 255)->nullable();
            $table->string('sub_title', 255)->nullable();
            $table->text('description')->nullable();
            $table->boolean('transfer_copyright_when_purchased')->default(0)->comment('0=No;1=Yes');
            $table->boolean('price_type')->default(0)->comment('0:fixed,1:Auction,2:Auction with Buy Now');
            $table->float('price_usd', 10, 0)->nullable()->default(0); 
            $table->float('price_eth', 10, 0)->nullable()->default(0); 
            $table->float('bid_price_usd', 10, 0)->nullable()->default(0); 
            $table->float('bid_price_eth', 10, 0)->nullable()->default(0); 
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
        Schema::dropIfExists('products');
    }
}
