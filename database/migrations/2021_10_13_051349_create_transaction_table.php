<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('server_id')->default(0);
            $table->integer('user_id');
            $table->integer('inquiry_id');
            $table->integer('division_id');
            $table->integer('ott_code');
            $table->string('ott_name');
            $table->string('title');
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->string('remark')->default('');
            $table->string('status_server')->default('');
            $table->string('receipt_server')->default('');
            $table->timestamp('approved_user_at')->nullable();
            $table->string('approved_user_by')->default('');
            $table->timestamp('approved_director_at')->nullable();
            $table->string('approved_director_by')->default('');
            $table->timestamp('rejected_director_at')->nullable();
            $table->string('rejected_director_by')->default('');
            $table->timestamp('approved_finance_at')->nullable();
            $table->string('approved_finance_by')->default('');
            $table->timestamp('rejected_finance_at')->nullable();
            $table->string('rejected_finance_by')->default('');
            $table->timestamp('transferred_at')->nullable();
            $table->string('transferred_by')->default('');
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
