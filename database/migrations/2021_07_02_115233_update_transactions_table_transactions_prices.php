<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionsTableTransactionsPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->float('paid_price', 10, 0)->nullable()->default(0)->after("transaction_hash"); 
            $table->float('earned_price', 10, 0)->nullable()->default(0)->after("paid_price"); ; 
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
            $table->dropColumn('paid_price');
            $table->dropColumn('earned_price');

        });
    }
}
