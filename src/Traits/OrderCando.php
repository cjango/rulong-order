<?php

namespace RuLong\Order\Traits;

use RuLong\Order\Models\Order;

trait OrderCando
{

    /**
     * 是否可取消
     * @Author:<C.Jason>
     * @Date:2018-10-22T17:11:14+0800
     * @return boolean
     */
    public function canCancel()
    {
        return true;
    }

    /**
     * 可申请退款
     * @Author:<C.Jason>
     * @Date:2018-10-22T17:11:45+0800
     * @return boolean
     */
    public function canRefund()
    {

    }

    /**
     * 可发货
     * @Author:<C.Jason>
     * @Date:2018-10-22T17:12:13+0800
     * @return boolean
     */
    public function canDeliver()
    {

    }

    /**
     * 可签收
     * @Author:<C.Jason>
     * @Date:2018-10-22T17:12:43+0800
     * @return boolean
     */
    public function canSingin()
    {

    }

    /**
     * 是否可支付
     * @Author:<C.Jason>
     * @Date:2018-10-22T17:13:08+0800
     * @return boolean
     */
    public function canPay()
    {

    }
}
