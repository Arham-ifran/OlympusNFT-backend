<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductReportAbusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_report_abuses', function (Blueprint $table) {
            $table->unsignedInteger('id',true);
            $table->string('title')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('product_report_abuses');
    }
}
