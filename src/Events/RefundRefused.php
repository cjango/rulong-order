<?php

namespace RuLong\Order\Events;

use RuLong\Order\Models\Refund;

/**
 * 拒绝退款
 */
class RefundRefused
{

    public $refund;

    public function __construct(Refund $refund)
    {
        $this->refund = $refund;
    }
}
