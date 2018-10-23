<?php

namespace RuLong\Order\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{

    const UPDATED_AT = null;

    protected $guarded = [];

    /**
     * 所属订单
     * @Author:<C.Jason>
     * @Date:2018-10-19T13:49:06+0800
     * @return Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
