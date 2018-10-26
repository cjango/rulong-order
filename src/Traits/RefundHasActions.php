<?php

namespace RuLong\Order\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuLong\Order\Events\RefundAgreed;
use RuLong\Order\Events\RefundCompleted;
use RuLong\Order\Events\RefundProcessed;
use RuLong\Order\Events\RefundRefused;
use RuLong\Order\Exceptions\RefundException;
use RuLong\Order\Models\Order;
use RuLong\Order\Models\Refund;

trait RefundHasActions
{

    /**
     * 取消退款单，想办法变回原来的状态
     * @Author:<C.Jason>
     * @Date:2018-10-26T11:27:29+0800
     * @return [type] [description]
     */
    public function cancel()
    {
        if (!$this->canCancel()) {
            throw new OrderException("退款单状态不可取消");
        }
    }

    /**
     * 同意退款
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:39:04+0800
     * @return RefundException|boolean
     */
    public function agree()
    {
        if (!$this->canAgree()) {
            throw new OrderException("退款单状态不可以同意退款");
        }

        DB::transaction(function () {
            $this->state        = Refund::REFUND_AGREE;
            $this->actual_total = $this->refund_total;
            $this->save();

            if (in_array($this->order->getOrderStatus('deliver'), [2, 3, 5])) {
                if ($this->order->express) {
                    // 如果已经发货，签收的，创建一个退款物流订单
                    $this->express()->create();
                }
                $this->order->setOrderStatus('deliver', 7);
            } elseif (in_array($this->order->getOrderStatus('deliver'), [0, 1, 4])) {
                $this->order->setOrderStatus('deliver', 6);
            }
            $this->order->setOrderStatus('pay', 6);
            $this->order->state = Order::REFUND_AGREE;
            $this->order->save();

            event(new RefundAgreed($this));
        });

        return true;
    }

    /**
     * 拒绝退款
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:40:07+0800
     * @param string|null $remark 拒绝原因
     * @return RefundException|boolean
     */
    public function refuse(string $remark = null)
    {
        if (!$this->canRefuse()) {
            throw new OrderException("退款单状态不可以拒绝退款");
        }

        DB::transaction(function () use ($remark) {
            $this->state  = Refund::REFUND_REFUSE;
            $this->remark = $remark;
            $this->save();

            $this->order->setOrderStatus('pay', 5);
            $this->order->state = Order::REFUND_REFUSE;
            $this->order->save();

            event(new RefundRefused($this));
        });

        return true;
    }

    /**
     * 退货退款中
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:40:29+0800
     * @return RefundException|boolean
     */
    public function deliver($company = null, $number = null)
    {
        if (!$this->canDeliver()) {
            throw new OrderException("退款单状态不可以拒绝退款");
        }

        DB::transaction(function () {
            $this->state       = Refund::REFUND_PROCESS;
            $this->refunded_at = Carbon::now();
            $this->save();

            $this->order->setOrderStatus('pay', 6);
            $this->order->state = Order::REFUND_PROCESS;
            $this->order->save();

            event(new RefundProcessed($this));
        });

        return true;
    }

    /**
     * 确认收货
     * @Author:<C.Jason>
     * @Date:2018-10-22T14:11:42+0800
     * @return OrderException|boolean
     */
    public function receive()
    {
        if (!$this->canReceive()) {
            throw new OrderException('退款单状态不可以确认收货');
        }

        $this->order->setOrderStatus('deliver', 8);
        $this->order->save();

        return true;
    }

    /**
     * 未收到
     * @Author:<C.Jason>
     * @Date:2018-10-22T14:11:42+0800
     * @return OrderException|boolean
     */
    public function unreceive()
    {
        if (!$this->canUnreceive()) {
            throw new OrderException('退款单状态不可以未收到商品');
        }

        $this->order->setOrderStatus('deliver', 9);
        $this->order->save();

        return true;
    }

    /**
     * 标记退款完成
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:40:36+0800
     * @return RefundException|boolean
     */
    public function complete()
    {
        if (!$this->canComplete()) {
            throw new OrderException("订单状态不可审核");
        }

        DB::transaction(function () {
            $this->state = Refund::REFUND_COMPLETED;
            $this->save();

            $this->order->state = Order::REFUND_COMPLETED;
            $this->order->save();

            event(new RefundCompleted($this));
        });

        return true;
    }

}
