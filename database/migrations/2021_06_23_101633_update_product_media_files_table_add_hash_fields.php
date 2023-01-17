<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductMediaFilesTableAddHashFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_media_files', function (Blueprint $table) {
            $table->string('ipfs_image_hash', 255)->nullable()->after('name');
            $table->string('ipfs_json_file_hash', 255)->nullable()->after('ipfs_image_hash');
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
            $table->dropColumn('ipfs_image_hash');
            $table->dropColumn('ipfs_json_file_hash');
        });
    }
}
