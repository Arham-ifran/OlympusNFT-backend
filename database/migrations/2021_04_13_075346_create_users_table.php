<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('user_type')->default(1)->comment('1=seller;2=customer');
            $table->string('firstname', 255);
            $table->string('email', 255)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->string('lastname', 255)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('address2', 100)->nullable();
            $table->string('city', 20)->nullable();
            $table->string('state', 20)->nullable();
            $table->string('country', 20)->nullable();
            $table->string('zipcode', 20)->nullable();
            $table->string('dob', 20)->nullable();
            $table->boolean('is_active')->default(0)->comment('0=inactive;1=active');
            $table->dateTime('last_login_on')->nullable();
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
        Schema::dropIfExists('users');
    }
}
