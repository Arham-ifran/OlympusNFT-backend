<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('template_type')->default(1);
            $table->string('email_type', 30)->nullable();
            $table->string('title', 100)->nullable();
            $table->text('content')->nullable();
            $table->text('header')->nullable();
            $table->text('footer')->nullable();
            $table->string('attachment', 200)->nullable();
            $table->string('subject', 200)->nullable();
            $table->boolean('is_active')->default(0);
            $table->boolean('is_default')->default(0);
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
        Schema::dropIfExists('templates');
    }
}
