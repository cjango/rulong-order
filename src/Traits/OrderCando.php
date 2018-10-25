<?php

namespace RuLong\Order\Traits;

use RuLong\Order\Models\Order;

trait OrderCando
{

    /**
     * 是否可以审核
     * @Author:<C.Jason>
     * @Date:2018-10-25T16:37:32+0800
     * @return boolean
     */
    public function canAudit(): bool
    {
        return ($this->state == Order::ORDER_INIT);
    }

    /**
     * 是否可支付
     * @Author:<C.Jason>
     * @Date:2018-10-22T17:13:08+0800
     * @return boolean
     */
    public function canPay(): bool
    {
        return ($this->state == Order::ORDER_UNPAID)
            && ($this->getOrderStatus('status') == 1)
            && ($this->getOrderStatus('pay') == 0);
    }

    /**
     * 是否可取消
     * @Author:<C.Jason>
     * @Date:2018-10-22T17:11:14+0800
     * @return boolean
     */
    public function canCancel(): bool
    {
        return (in_array($this->state, [Order::ORDER_INIT, Order::ORDER_UNPAID]))
            && (in_array($this->getOrderStatus('status'), [0, 1]))
            && ($this->getOrderStatus('pay') == 0);
    }

    /**
     * 可发货
     * @Author:<C.Jason>
     * @Date:2018-10-22T17:12:13+0800
     * @return boolean
     */
    public function canDeliver(): bool
    {
        return (in_array($this->state, [Order::ORDER_PAID, Order::ORDER_DELIVER]))
            && ($this->getOrderStatus('status') == 1)
            && ($this->getOrderStatus('pay') == 1)
            && ($this->getOrderStatus('deliver') == 0);
    }

    /**
     * 可签收
     * @Author:<C.Jason>
     * @Date:2018-10-22T17:12:43+0800
     * @return boolean
     */
    public function canSingin(): bool
    {
        return ($this->state == Order::ORDER_DELIVERED)
            && ($this->getOrderStatus('status') == 1)
            && ($this->getOrderStatus('pay') == 1)
            && (in_array($this->getOrderStatus('deliver'), [1, 2]));
    }

    /**
     * 可延迟收货
     * @Author:<C.Jason>
     * @Date:2018-10-25T17:17:01+0800
     * @return boolean
     */
    public function canDelay(): bool
    {
        return ($this->state == Order::ORDER_DELIVERED)
            && ($this->getOrderStatus('status') == 1)
            && ($this->getOrderStatus('pay') == 1)
            && ($this->getOrderStatus('deliver') == 2);
    }

    /**
     * 可设置未收到
     * @Author:<C.Jason>
     * @Date:2018-10-25T17:17:32+0800
     * @return boolean
     */
    public function canUnreceive(): bool
    {
        return ($this->state == Order::ORDER_DELIVERED)
            && ($this->getOrderStatus('status') == 1)
            && ($this->getOrderStatus('pay') == 1)
            && (in_array($this->getOrderStatus('deliver'), [2, 3]));
    }

    /**
     * 可完成订单
     * @Author:<C.Jason>
     * @Date:2018-10-25T17:35:12+0800
     * @return boolean
     */
    public function canComplete(): bool
    {
        return false;
    }

    /**
     * 可关闭订单
     * @Author:<C.Jason>
     * @Date:2018-10-25T17:37:03+0800
     * @return boolean
     */
    public function canClose(): bool
    {
        return false;
    }

    /**
     * 可申请退款
     * @Author:<C.Jason>
     * @Date:2018-10-22T17:11:45+0800
     * @return boolean
     */
    public function canRefund(): bool
    {
        return (in_array($this->state, [Order::ORDER_DELIVERED, Order::ORDER_SIGNED]))
            && ($this->getOrderStatus('status') == 1)
            && ($this->getOrderStatus('pay') == 1)
            && (in_array($this->getOrderStatus('deliver'), [1, 2, 3, 4, 5]));
    }
}
