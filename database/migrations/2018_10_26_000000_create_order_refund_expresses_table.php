<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderRefundExpressesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_refund_expresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('refund_id')->unsigned()->index('refund_id');
            $table->string('company', 32)->nullable();
            $table->string('number', 32)->nullable();
            $table->dateTime('deliver_at')->nullable()->comment('发货时间');
            $table->dateTime('receive_at')->nullable()->comment('收到时间');
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
        Schema::drop('order_refund_expresses');
    }

}
