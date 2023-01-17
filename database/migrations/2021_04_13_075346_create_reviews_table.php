<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('product_id')->nullable();
            $table->bigInteger('seller_id')->nullable();
            $table->bigInteger('buyer_id')->nullable();
            $table->boolean('rating')->default(0);
            $table->text('review')->nullable();
            $table->boolean('is_active')->default(0)->comment('0=inactive;1=active');
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
        Schema::dropIfExists('reviews');
    }
}
