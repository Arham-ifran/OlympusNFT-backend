<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCmsPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 100)->nullable();
            $table->text('tracking_code')->nullable();
            $table->string('seo_url', 100)->nullable();
            $table->boolean('is_static')->default(0);
            $table->text('description')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_title', 200)->nullable();
            $table->text('meta_keywords')->nullable();
            $table->boolean('show_in_header')->default(0);
            $table->boolean('show_in_footer')->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->boolean('sort_by')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_pages');
    }
}
