<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionXendits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_xendits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_id');
            $table->string('server_id');
            $table->string('server_status');
            $table->decimal('fee', 15, 2)->default(0);
            $table->text('response_dump');
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
