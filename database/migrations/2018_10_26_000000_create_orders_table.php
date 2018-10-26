<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('orderid', 32)->unique('orderid')->comment('订单编号');
            $table->integer('user_id')->unsigned();
            $table->decimal('amount', 20, 3)->unsigned()->comment('商品金额');
            $table->decimal('freight', 10, 3)->unsigned()->nullable()->comment('运费');
            $table->string('status', 16)->default('0000')->comment('4码订单状态');
            $table->string('state', 16)->comment('状态');
            $table->string('remark')->nullable()->comment('订单备注');
            $table->dateTime('paid_at')->nullable()->comment('支付时间');
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
        Schema::drop('orders');
    }

}
