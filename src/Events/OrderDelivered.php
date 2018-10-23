<?php

namespace RuLong\Order\Events;

use RuLong\Order\Models\Order;

/**
 * 订单发货完成
 */
class OrderDelivered
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
