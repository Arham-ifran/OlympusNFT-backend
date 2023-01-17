<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionsTableAddRelationTableId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('transaction_of')->nullable()->comment('0=ad;1=product;2:order')->after("user_id");
            $table->biginteger('ad_id')->unsigned()->nullable()->after("transaction_of");
            $table->foreign('ad_id')->references('id')->on('ads')->onUpdate('RESTRICT')->onDelete('SET NULL');
            $table->biginteger('product_id')->unsigned()->nullable()->after("ad_id");;
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('SET NULL');
            $table->biginteger('order_id')->unsigned()->nullable()->after("product_id");
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('RESTRICT')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('transaction_of');
            $table->dropColumn('ad_id');
            $table->dropColumn('product_id');
            $table->dropColumn('order_id');
          
        });
    }
}
