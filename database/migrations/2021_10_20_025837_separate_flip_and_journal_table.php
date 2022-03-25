<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeparateFlipAndJournalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('division_flips', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('division_id');
            $table->string('flip_name');
            $table->string('flip_key');
            $table->string('flip_token');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('division_journals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('division_id');
            $table->string('journal_name');
            $table->string('journal_key');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('divisions', function (Blueprint $table) {
            $table->dropColumn('flip_name');
            $table->dropColumn('flip_key');
            $table->dropColumn('flip_token');
            $table->dropColumn('journal_name');
            $table->dropColumn('journal_key');
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
