<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->biginteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->boolean('type')->default(0)->comment('0=Transfer;1=Mint');
            $table->string('from_address', 100)->nullable();
            $table->string('to_address', 100)->nullable();
            $table->string('transaction_hash', 100)->nullable();
            $table->boolean('transaction_status')->default(1)->comment('0=pending;1=completed');
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
        Schema::dropIfExists('transactions');
    }
}
