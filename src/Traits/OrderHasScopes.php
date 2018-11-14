<?php

namespace RuLong\Order\Traits;

use RuLong\Order\Models\Order;

trait OrderHasScopes
{

    public function scopeUnpaid($query)
    {
        return $query->where('state', Order::ORDER_UNPAY);
    }

    public function scopeUnDeliver($query)
    {
        return $query->whereIn('state', [Order::ORDER_PAID, Order::ORDER_DELIVER]);
    }

    public function scopeDelivered($query)
    {
        return $query->where('state', Order::ORDER_DELIVERED);
    }

    public function scopeSigned($query)
    {
        return $query->where('state', Order::ORDER_SIGNED);
    }
}
