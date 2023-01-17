<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->biginteger('product_id')->unsigned();
            $table->foreign('product_id', 'product_id')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->biginteger('bidder_id')->unsigned();
            $table->foreign('bidder_id', 'bidder_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->float('price', 10, 0)->nullable()->default(0); 
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
        Schema::dropIfExists('bids');
    }
}
