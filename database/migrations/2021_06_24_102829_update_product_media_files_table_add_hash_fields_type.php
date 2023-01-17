<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductMediaFilesTableAddHashFieldsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_media_files', function (Blueprint $table) {
            $table->text('ipfs_image_hash')->change();
            $table->text('ipfs_json_file_hash')->change();
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
