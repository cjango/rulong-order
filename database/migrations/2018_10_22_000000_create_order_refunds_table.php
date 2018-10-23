<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderRefundsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned()->comment('关联订单id');
            $table->string('orderid', 20)->unique('orderid')->comment('退款单号');
            $table->decimal('refund_total', 20, 3)->unsigned()->comment('退款金额');
            $table->decimal('actual_total', 20, 3)->unsigned()->comment('实退金额');
            $table->string('state', 16);
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
        Schema::drop('order_refunds');
    }

}
