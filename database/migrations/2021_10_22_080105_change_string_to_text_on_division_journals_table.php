<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeStringToTextOnDivisionJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('division_flips', function (Blueprint $table) {
            $table->text('flip_key')->change();
            $table->text('flip_token')->change();
        });

        Schema::table('division_journals', function (Blueprint $table) {
            $table->text('journal_key')->change();
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
