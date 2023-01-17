<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firstname', 30);
            $table->string('lastname', 30);
            $table->string('email', 255)->unique();
            $table->string('password')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('city', 20)->nullable();
            $table->string('state', 20)->nullable();
            $table->string('country', 20)->nullable();
            $table->string('zipcode', 20)->nullable();
            $table->boolean('is_active')->default(0)->comment('0=inactive;1=active');
            $table->dateTime('last_login_on')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->string('photo', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
