<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->bigInteger('start_date'); 
            $table->bigInteger('end_date'); 
            $table->bigInteger('impression')->default(0);
            $table->float('cpc', 10, 0)->nullable()->default(0);  
            $table->float('total_budget', 10, 0)->nullable()->default(0); 
            $table->float('total_spent', 10, 0)->nullable()->default(0);
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
        Schema::dropIfExists('ads');
    }
}
