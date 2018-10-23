<?php

namespace RuLong\Order\Events;

use RuLong\Order\Models\Order;

/**
 * 未收到货物
 */
class OrderUnreceived
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
