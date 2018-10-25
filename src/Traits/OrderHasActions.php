<?php

namespace RuLong\Order\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuLong\Order\Events\OrderCanceled;
use RuLong\Order\Events\OrderClosed;
use RuLong\Order\Events\OrderCompleted;
use RuLong\Order\Events\OrderDelaied;
use RuLong\Order\Events\OrderDelivered;
use RuLong\Order\Events\OrderPaid;
use RuLong\Order\Events\OrderSignined;
use RuLong\Order\Events\OrderUnreceived;
use RuLong\Order\Exceptions\OrderException;
use RuLong\Order\Models\Order;

trait OrderHasActions
{

    /**
     * 订单审核
     * @Author:<C.Jason>
     * @Date:2018-10-25T16:18:05+0800
     * @param boolean $result 审核结果
     * @return OrderException|boolean
     */
    public function audit(bool $result)
    {
        if (!$this->canAudit()) {
            throw new OrderException("订单状态不可审核");
        }

        if ($result === true) {
            // 审核通过，设置订单为 未支付状态，分状态 进行中
            $this->state = Order::ORDER_UNPAID;
            $this->setOrderStatus('status', 1);
        } else {
            // 审核不通过，设置订单为 取消状态，分状态 系统取消
            $this->state = Order::ORDER_CANCEL;
            $this->setOrderStatus('status', 4);
        }

        return $this->save();
    }

    /**
     * 标记订单已支付状态
     * @Author:<C.Jason>
     * @Date:2018-10-22T10:16:02+0800
     * @return OrderException|boolean
     */
    public function paid()
    {
        if (!$this->canPay()) {
            throw new OrderException("订单状态不可支付");
        }

        $this->setOrderStatus('pay', 1);
        $this->state   = Order::ORDER_PAID;
        $this->paid_at = Carbon::now();
        $this->save();

        event(new OrderPaid($this));

        return true;
    }

    /**
     * 取消订单
     * @Author:<C.Jason>
     * @Date:2018-10-25T16:57:43+0800
     * @param  integer $channel 订单取消渠道
     * @return OrderException|boolean
     */
    public function cancel(int $channel)
    {
        if (!$this->canCancel()) {
            throw new OrderException("订单状态不可取消");
        }

        $this->setOrderStatus('status', $channel);
        $this->state = Order::ORDER_CANCEL;
        $this->save();

        event(new OrderCanceled($this));

        return true;
    }

    /**
     * 标记发货处理中
     * @Author:<C.Jason>
     * @Date:2018-10-22T13:27:34+0800
     * @return void
     */
    public function delivering()
    {
        if (!$this->canDeliver()) {
            throw new OrderException('订单状态不可发货');
        }

        if ($this->state = Order::ORDER_PAID) {
            $this->state = Order::ORDER_DELIVER;
            $this->save();
        }
    }

    /**
     * 订单发货
     * @Author:<C.Jason>
     * @Date:2018-10-25T17:14:53+0800
     * @param string|null $company 物流名称
     * @param string|null $number  物流单号
     * @return OrderException|boolean
     */
    public function deliver($company = null, $number = null)
    {
        if (!$this->canDeliver()) {
            throw new OrderException('订单状态不可发货');
        }

        DB::transaction(function () use ($company, $number) {
            if ($this->express) {
                $this->express->update([
                    'company'    => $company,
                    'number'     => $number,
                    'deliver_at' => Carbon::now(),
                ]);
                $this->setOrderStatus('deliver', 2);
            } else {
                $this->setOrderStatus('deliver', 1);
            }

            $this->state = Order::ORDER_DELIVERED;
            $this->save();

            event(new OrderDelivered($this));
        });

        return true;
    }

    /**
     * 签收订单
     * @Author:<C.Jason>
     * @Date:2018-10-22T13:47:06+0800
     * @return OrderException|boolean
     */
    public function signin()
    {
        if (!$this->canSingin()) {
            throw new OrderException('订单状态不可签收');
        }

        DB::transaction(function () {
            if ($this->express) {
                $this->express->update([
                    'receive_at' => Carbon::now(),
                ]);
            }

            $this->setOrderStatus('deliver', 5);
            $this->state = Order::ORDER_SIGNED;
            $this->save();

            event(new OrderSignined($this));
        });

        return true;
    }

    /**
     * 延迟收货
     * @Author:<C.Jason>
     * @Date:2018-10-22T14:09:15+0800
     * @return OrderException|boolean
     */
    public function delay()
    {
        if (!$this->canDelay()) {
            throw new OrderException('订单状态不可延迟收货');
        }

        $this->setOrderStatus('deliver', 3);
        $this->save();

        event(new OrderDelaied($this));

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
            throw new OrderException('订单状态不可延迟收货');
        }

        $this->setOrderStatus('deliver', 4);
        $this->save();

        event(new OrderUnreceived($this));

        return true;
    }

    /**
     * 标记订单完成状态
     * @Author:<C.Jason>
     * @Date:2018-10-25T17:28:01+0800
     * @return OrderException|boolean
     */
    public function complete()
    {
        if (!$this->canComplete()) {
            throw new OrderException('订单状态不可完成');
        }

        $this->setOrderStatus('status', 8);
        $this->state = Order::ORDER_COMPLETED;
        $this->save();

        event(new OrderCompleted($this));

        return true;
    }

    /**
     * 关闭订单
     * @Author:<C.Jason>
     * @Date:2018-10-22T14:14:34+0800
     * @return OrderException|boolean
     */
    public function close()
    {
        if (!$this->canClose()) {
            throw new OrderException('订单状态不可关闭');
        }

        $this->setOrderStatus('status', 9);
        $this->state = Order::ORDER_CLOSED;
        $this->save();

        event(new OrderClosed($this));

        return true;
    }

    /**
     * 申请退款，创建退款单
     * @Author:<C.Jason>
     * @Date:2018-10-23T14:10:54+0800
     * @param array $items 退款项目
     * [
     *     ['item_id' => integer, 'number' => integer],
     *     ['item_id' => integer, 'number' => integer],
     * ]
     * @param float $total 申请退款金额
     */
    public function createRefund(array $items, float $total = null)
    {
        return \Orders::refund($this, $items, $total);
    }
}
