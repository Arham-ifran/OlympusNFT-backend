<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->biginteger('category_id')->unsigned()->nullable();
            $table->foreign('category_id', 'category_id')->references('id')->on('categories')->onUpdate('RESTRICT')->onDelete('SET NULL');
            $table->string('store_title', 255)->nullable();
            $table->string('sub_title', 255)->nullable();
            $table->string('store_tags', 255)->nullable()->comment('comma seperated values');
            $table->string('image', 255)->nullable();
            $table->text('description')->nullable();
            $table->boolean('store_your_data')->default(0)->comment('0=Mintable;1=IPFS,2:Server');
            $table->integer('royalty_amount')->default(0)->comment('0 to 90 %');
            $table->boolean('increase_batch_minting')->default(0)->comment('0=No;1=Yes');  
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
        Schema::dropIfExists('stores');
    }
}
