<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMemberOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_member_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_company_id');
            $table->string('invoice_number');
            $table->string('email');
            $table->string('package_name');
            $table->decimal('package_price', 15, 2);
            $table->string('long_expired');
            $table->timestamp('paid_at')->nullable();
            $table->string('paid_with')->default('');
            $table->tinyInteger('is_active')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('user_companies', function (Blueprint $table) {
            $table->string('package_name')->default('')->after('title');
            $table->timestamp('expired_at')->nullable()->after('package_name');
        });

        Schema::create('user_short_carts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_member_order_id');
            $table->string('invoice_payment');
            $table->string('status');
            $table->string('trx_type');
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
        //
    }
}
