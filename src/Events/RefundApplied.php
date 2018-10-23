<?php

namespace RuLong\Order\Events;

use RuLong\Order\Models\Order;
use RuLong\Order\Models\Refund;

/**
 * 订单申请退款
 */
class RefundApplied
{

    public $order;

    public $refund;

    public function __construct(Order $order, Refund $refund)
    {
        $this->order  = $order;
        $this->refund = $refund;
    }
}
