<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDivisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->default('')->after('password');
        });

        Schema::create('divisions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_company_id');
            $table->string('title');
            $table->string('director_email');
            $table->string('director_phone');
            $table->string('flip_name')->default('');
            $table->string('flip_key')->default('');
            $table->string('flip_token')->default('');
            $table->string('journal_name')->default('');
            $table->string('journal_key')->default('');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('division_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('division_id');
            $table->integer('user_id');
            $table->string('role')->default('operator');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
