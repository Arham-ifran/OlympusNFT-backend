<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSocialMediaFieldsInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 100)->nullable()->after('user_type');
            
            $table->string('wallet_address', 255)->nullable()->after('dob');
            $table->string('twitter', 255)->nullable()->after('wallet_address');
            $table->string('instagram', 255)->nullable()->after('twitter'); 
            $table->string('reedit', 255)->nullable()->after('instagram');  
            $table->boolean('email_notification')->default(0)->comment('0=Disable;1=Enable')->after('reedit');
            $table->boolean('is_active')->default(0)->comment('0=inactive;1=active;2=block')->change();
            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropColumn('wallet_address');
            $table->dropColumn('twitter');
            $table->dropColumn('instagram');
            $table->dropColumn('reedit');
            $table->dropColumn('email_notification');
        });
    }
}
