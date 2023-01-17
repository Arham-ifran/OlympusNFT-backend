<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('site_logo', 255)->nullable();
            $table->string('site_name', 255)->nullable();
            $table->string('site_title', 255)->nullable();
            $table->text('site_keywords')->nullable();
            $table->text('site_description')->nullable();
            $table->string('site_email', 255)->nullable();
            $table->string('inquiry_email', 255)->nullable();
            $table->string('site_phone', 255)->nullable();
            $table->string('site_mobile', 255)->nullable();
            $table->text('site_address')->nullable();
            $table->string('facebook', 255)->nullable();
            $table->string('twitter', 255)->nullable();
            $table->string('linkedin', 255)->nullable();
            $table->string('insta', 255)->nullable();
            $table->string('skype', 255)->nullable();
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
        Schema::dropIfExists('site_settings');
    }
}
