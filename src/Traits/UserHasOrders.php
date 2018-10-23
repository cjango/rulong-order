<?php

namespace RuLong\Order\Traits;

use RuLong\Order\Models\Order;

trait UserHasOrders
{

    /**
     * 用户订单
     * @Author:<C.Jason>
     * @Date:2018-10-15T14:56:07+0800
     * @return \RuLong\Order\Models\Order::class
     */
    public function orders()
    {
        return $this->hasOne(hasMany::class);
    }

}
