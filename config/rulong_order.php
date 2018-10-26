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

    /**
     * 订单自动审核
     */
    'auto_audit'     => true,

    /**
     * N天后无事件的订单 可完成
     */
    'completed_days' => 7,

    'admin_guard'    => 'rulong',
];
