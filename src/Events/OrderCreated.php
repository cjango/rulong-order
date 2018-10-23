<?php

namespace RuLong\Order\Events;

use RuLong\Order\Models\Order;

/**
 * 订单支付完成事件
 */
class OrderCreated
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
