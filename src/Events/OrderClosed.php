<?php

namespace RuLong\Order\Events;

use RuLong\Order\Models\Order;

/**
 * 订单关闭
 */
class OrderClosed
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
