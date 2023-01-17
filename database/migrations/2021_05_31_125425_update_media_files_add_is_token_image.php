<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMediaFilesAddIsTokenImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_media_files', function (Blueprint $table) {
            $table->boolean('is_token_image')->default(0)->comment('0=No;1=Yes')->after("name");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_media_files', function (Blueprint $table) {
            //
        });
    }
}
