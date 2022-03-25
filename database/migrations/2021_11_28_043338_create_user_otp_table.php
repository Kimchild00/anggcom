<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserOtpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_otp', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('users_id');
            $table->string('code_email')->nullable();
            $table->string('code_google_auth')->nullable();
            $table->timestamp('time_expired');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_otp', function (Blueprint $table) {
            $table->drop('users_otp');
        });
    }
}
