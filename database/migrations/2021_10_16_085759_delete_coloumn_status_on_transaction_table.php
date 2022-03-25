<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteColoumnStatusOnTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->dropColumn('approved_user_at');
            $table->dropColumn('approved_user_by');
            $table->dropColumn('approved_director_at');
            $table->dropColumn('approved_director_by');
            $table->dropColumn('rejected_director_at');
            $table->dropColumn('rejected_director_by');
            $table->dropColumn('approved_finance_at');
            $table->dropColumn('approved_finance_by');
            $table->dropColumn('rejected_finance_at');
            $table->dropColumn('rejected_finance_by');
            $table->dropColumn('transferred_at');
            $table->dropColumn('transferred_by');
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
