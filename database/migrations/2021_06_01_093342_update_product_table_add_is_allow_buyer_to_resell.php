<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductTableAddIsAllowBuyerToResell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('token_id',255)->nullable()->change();
            $table->string('token_address',255)->nullable()->change();
            $table->boolean('is_allow_buyer_to_resell')->default(1)->comment('0=No;1=Yes')->after("token_metadata");
            $table->string('listing_tag', 255)->nullable()->comment('comma seperated value')->after("sub_title");
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
            $table->dropColumn('is_allow_buyer_to_resell');
            $table->dropColumn('listing_tag');
        });
    }
}
