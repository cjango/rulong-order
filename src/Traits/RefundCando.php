<?php

namespace RuLong\Order\Traits;

use RuLong\Order\Models\Order;
use RuLong\Order\Models\Refund;

trait RefundCando
{

    /**
     * 可同意退款
     * @Author:<C.Jason>
     * @Date:2018-10-26T11:18:00+0800
     * @return boolean
     */
    public function canAgree(): bool
    {
        return ($this->state == Refund::REFUND_APPLY)
            && ($this->order->state == Order::REFUND_APPLY)
            && ($this->order->getOrderStatus('status') == 1)
            && (in_array($this->order->getOrderStatus('pay'), [2, 3, 4]))
            && (in_array($this->order->getOrderStatus('deliver'), [0, 1, 2, 3, 4, 5, 6]));
    }

    /**
     * 可以拒绝退款
     * @Author:<C.Jason>
     * @Date:2018-10-26T16:13:46+0800
     * @return boolean
     */
    public function canRefuse(): bool
    {
        return $this->canAgree();
    }

    /**
     * 可以退货
     * @Author:<C.Jason>
     * @Date:2018-10-26T16:13:56+0800
     * @return boolean
     */
    public function canDeliver(): bool
    {
        return ($this->state == Refund::REFUND_AGREE)
            && ($this->order->state == Order::REFUND_AGREE)
            && ($this->order->getOrderStatus('status') == 1)
            && ($this->order->getOrderStatus('pay') == 6)
            && ($this->order->getOrderStatus('deliver') == 7);
    }

    public function canReceive(): bool
    {
        return true;
    }

    public function canUnreceive(): bool
    {
        return true;
    }

    /**
     * 是否可以完成退款流程
     * 完成之后可以走退款接口了
     * @Author:<C.Jason>
     * @Date:2018-10-26T16:28:21+0800
     * @return boolean
     */
    public function canComplete(): bool
    {
        return (
            ($this->state == Refund::REFUND_PROCESS)
            && ($this->order->state == Order::REFUND_PROCESS)
            && ($this->order->getOrderStatus('status') == 1)
            && ($this->order->getOrderStatus('pay') == 6)
            && ($this->order->getOrderStatus('deliver') == 8)
        ) || (
            ($this->state == Refund::REFUND_AGREE)
            && ($this->order->getOrderStatus('status') == 1)
            && ($this->order->getOrderStatus('pay') == 6)
            && ($this->order->getOrderStatus('status') == 6)
        );

        return true;
    }

    /**
     * 是否可以取消退款单
     * @Author:<C.Jason>
     * @Date:2018-10-26T16:28:52+0800
     * @return [type] [description]
     */
    public function canCancel(): bool
    {
        return true;
    }

}
