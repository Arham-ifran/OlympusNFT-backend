<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->biginteger('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->biginteger('seller_id')->unsigned();
            $table->foreign('seller_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->biginteger('buyer_id')->unsigned();
            $table->foreign('buyer_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->float('price_usd', 10, 0)->nullable()->default(0);
            $table->float('price_eth', 10, 0)->nullable()->default(0);
            $table->float('total', 10, 0)->nullable()->default(0);
            $table->float('is_auction_product', 10, 0)->default(0)->comment('0=No;1=yes');
            $table->biginteger('bid_id')->unsigned()->nullable();
            $table->foreign('bid_id')->references('id')->on('bids')->onUpdate('RESTRICT')->onDelete('SET NULL');
            $table->float('bid_price_usd', 10, 0)->nullable()->default(0);
            $table->float('bid_price_eth', 10, 0)->nullable()->default(0);
            $table->biginteger('order_status_id')->unsigned()->nullable();
            $table->foreign('order_status_id')->references('id')->on('order_statuses')->onUpdate('RESTRICT')->onDelete('SET NULL');
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
        Schema::dropIfExists('orders');
    }
}
