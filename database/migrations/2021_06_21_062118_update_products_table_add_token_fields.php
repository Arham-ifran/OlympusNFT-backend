<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsTableAddTokenFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('token_name', 255)->nullable()->after('token_id');
            $table->string('contract_address', 255)->nullable()->after('token_metadata');
            $table->string('original_image', 255)->nullable()->after('contract_address');
            $table->string('original_creator', 255)->nullable()->after('original_image');
          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('token_name');
            $table->dropColumn('contract_address');
            $table->dropColumn('original_image');
            $table->dropColumn('original_creator');
        });
    }
}
