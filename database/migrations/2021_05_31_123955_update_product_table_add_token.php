<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductTableAddToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('token_id')->nullable()->after('bid_price_eth');
            $table->text('token_address')->nullable()->after('token_id');
            $table->text('token_metadata')->nullable()->after('token_address');
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
            $table->dropColumn('token_id');
            $table->dropColumn('token_address');
            $table->dropColumn('token_metadata');
        });
    }
}
