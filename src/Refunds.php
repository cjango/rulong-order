<?php

namespace RuLong\Order;

use RuLong\Order\Models\Order;
use RuLong\Order\Models\Refund;

class Refunds
{

    /**
     * 申请退款
     * @Author:<C.Jason>
     * @Date:2018-10-23T10:35:42+0800
     * @param Order $order 要退款的订单
     * @param array $items 退款项目
     * [
     *     ['item_id' => integer, 'number' => integer],
     *     ['item_id' => integer, 'number' => integer],
     * ]
     * @param float $total 申请退款金额
     * @return [type] [description]
     */
    public function create(Order $order, array $items, float $total = null)
    {
        return \Orders::refund($order, $items, $total);
    }

    /**
     * [agree description]
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:20:41+0800
     * @param Refund $refund [description]
     * @return RefundException
     */
    public function agree(Refund $refund)
    {
        return $refund->agree();
    }

    /**
     * [refuse description]
     * @Author:<C.Jason>
     * @Date:2018-10-23T15:17:18+0800
     * @param Refund $refund [description]
     * @param string|null $remark [description]
     * @return [type] [description]
     */
    public function refuse(Refund $refund, string $remark = null)
    {
        return $refund->refuse($remark);
    }

    /**
     * [process description]
     * @Author:<C.Jason>
     * @Date:2018-10-23T15:17:21+0800
     * @param Refund $refund [description]
     * @return [type] [description]
     */
    public function process(Refund $refund)
    {
        return $refund->process();
    }

    /**
     * [completed description]
     * @Author:<C.Jason>
     * @Date:2018-10-23T15:17:23+0800
     * @param Refund $refund [description]
     * @return [type] [description]
     */
    public function complete(Refund $refund)
    {
        return $refund->complete();
    }

}
