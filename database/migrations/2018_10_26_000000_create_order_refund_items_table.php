<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderRefundItemsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_refund_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('refund_id')->unsigned()->comment('退款单ID');
            $table->integer('order_id')->unsigned()->comment('订单ID');
            $table->integer('order_detail_id')->unsigned()->comment('订单详情ID');
            $table->integer('item_id')->unsigned()->comment('产品ID');
            $table->string('item_type');
            $table->integer('number')->unsigned()->comment('退货数量');
            $table->decimal('price', 20, 3)->unsigned();
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('order_refund_items');
    }

}
