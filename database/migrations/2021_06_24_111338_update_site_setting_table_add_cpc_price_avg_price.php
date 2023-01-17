<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSiteSettingTableAddCpcPriceAvgPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->float('current_average_cpc_price', 10, 0)->nullable()->after('skype');
            $table->float('suggested_cpc_pricr', 10, 0)->nullable()->after('current_average_cpc_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('current_average_cpc_price');
            $table->dropColumn('suggested_cpc_pricr');
        });
    }
}
