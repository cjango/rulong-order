<?php

namespace RuLong\Order\Events;

use RuLong\Order\Models\Order;

/**
 * 延迟收货
 */
class OrderDelaied
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
