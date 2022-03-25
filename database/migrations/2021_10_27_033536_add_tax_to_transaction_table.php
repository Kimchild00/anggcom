<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaxToTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_flips', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_id');
            $table->integer('server_id');
            $table->string('server_status');
            $table->decimal('fee', 15, 2)->default(0);
            $table->string('server_receipt');
            $table->text('response_dump');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('server_id');
            $table->dropColumn('fee');
            $table->dropColumn('status_server');
            $table->dropColumn('receipt_server');
            $table->dropColumn('response_dump');
        });

        Schema::create('transaction_taxes',function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_id');
            $table->string('type');
            $table->decimal('amount');
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
