<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_threads', function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('seller_id')->nullable();
            $table->bigInteger('buyer_id')->nullable();
            $table->integer('product_id')->nullable()->comment('product id');
            $table->text('max_message')->nullable();
            $table->boolean('is_read')->default(0)->comment('1=yes;0=not read');
            $table->timestamps();
            $table->index(['seller_id', 'buyer_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_threads');
    }
}
