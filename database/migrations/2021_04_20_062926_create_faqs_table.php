<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('category_id')->comment('faq category id');
            $table->foreign('category_id')->references('id')->on('faq_categories')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->string('title', 200)->nullable();
            $table->binary('description')->nullable();
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
        Schema::dropIfExists('faqs');
    }
}
