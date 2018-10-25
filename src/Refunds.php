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
     * 同意退款
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:20:41+0800
     * @param Refund $refund 退款单实例
     * @return RefundException|boolean
     */
    public function agree(Refund $refund)
    {
        return $refund->agree();
    }

    /**
     * 拒绝退款
     * @Author:<C.Jason>
     * @Date:2018-10-23T15:17:18+0800
     * @param Refund $refund 退款单实例
     * @param string|null $remark 拒绝备注
     * @return RefundException|boolean
     */
    public function refuse(Refund $refund, string $remark = null)
    {
        return $refund->refuse($remark);
    }

    /**
     * 退款中
     * @Author:<C.Jason>
     * @Date:2018-10-23T15:17:21+0800
     * @param Refund $refund 退款单实例
     * @return RefundException|boolean
     */
    public function process(Refund $refund)
    {
        return $refund->process();
    }

    /**
     * 退款完成
     * @Author:<C.Jason>
     * @Date:2018-10-23T15:17:23+0800
     * @param Refund $refund 退款单实例
     * @return RefundException|boolean
     */
    public function complete(Refund $refund)
    {
        return $refund->complete();
    }

}
