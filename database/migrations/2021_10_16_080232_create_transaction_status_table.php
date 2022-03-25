<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_status', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_id');
            $table->string('title');
            $table->integer('user_id');
            $table->string('message')->default('');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('transaction', function (Blueprint $table) {
            $table->string('current_status')->default('')->after('response_dump');
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
