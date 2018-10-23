<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderExpressesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_expresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned()->index('order_id');
            $table->string('name', 32)->nullable();
            $table->string('mobile', 32)->nullable();
            $table->string('address')->nullable();
            $table->string('company', 32)->nullable()->comment('物流公司');
            $table->string('number', 32)->nullable()->comment('物流单号');
            $table->dateTime('deliver_at')->nullable()->comment('发货时间');
            $table->dateTime('receive_at')->nullable()->comment('签收时间');
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
        Schema::drop('order_expresses');
    }

}
