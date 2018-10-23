<?php

return [
    /**
     * 用户模型
     */
    'user_model'     => \App\User::class,

    /**
     * 订单编号规则
     */
    'order_orderid'  => [
        'length' => 20,
        'prefix' => '',
    ],

    /**
     * 退款单号规则
     */
    'refund_orderid' => [
        'length' => 20,
        'prefix' => 'R',
    ],
];
