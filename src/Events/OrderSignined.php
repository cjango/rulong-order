<?php

namespace RuLong\Order\Events;

use RuLong\Order\Models\Order;

/**
 * 订单签收完成
 */
class OrderSignined
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
